# ====================================================
#   وكيل الطباعة المحلي - يتصل بـ localhost
# ====================================================

$BaseUrl  = "https://app.marsksa.com"
$ApiToken = "NknpM4KHqoVKpwhxXS0eJFfUBGmBu47k"
$LogFile  = Join-Path $PSScriptRoot "printing.log"

function Log-Message {
    param([string]$Message, [string]$Type = "INFO")
    $Timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
    $LogEntry  = "[$Timestamp] [$Type] $Message"
    Write-Host $LogEntry
    Add-Content -Path $LogFile -Value $LogEntry -Encoding UTF8
}

function Test-PrinterConnection {
    param([string]$Ip, [int]$Port)
    if ([string]::IsNullOrWhiteSpace($Ip)) { return "Not Configured" }
    try {
        $tcp    = New-Object System.Net.Sockets.TcpClient
        $result = $tcp.BeginConnect($Ip, $Port, $null, $null)
        $ok     = $result.AsyncWaitHandle.WaitOne([timespan]::FromSeconds(1))
        if ($ok) { $tcp.EndConnect($result); $tcp.Close(); return "Online" }
        return "Offline"
    } catch {
        return "Error"
    }
}

Log-Message "===== بدء وكيل الطباعة =====" "START"
Log-Message "السيرفر: $BaseUrl" "INFO"

$LastStatusUpdate = (Get-Date).AddMinutes(-5)

while ($true) {
    try {
        $response = Invoke-RestMethod -Uri "$BaseUrl/api/print-jobs?token=$ApiToken" -Method Get -TimeoutSec 5 -ErrorAction Stop

        $jobs     = $response.jobs
        $printers = $response.printers
        $port     = [int]$response.port

        if ((Get-Date) -gt $LastStatusUpdate.AddMinutes(1)) {
            $statuses = @{
                "cashier" = Test-PrinterConnection -Ip $printers.cashier -Port $port
                "kitchen" = Test-PrinterConnection -Ip $printers.kitchen -Port $port
                "shisha"  = Test-PrinterConnection -Ip $printers.shisha  -Port $port
            }
            $body = $statuses | ConvertTo-Json
            $postBody = @{ token = $ApiToken; statuses = $statuses } | ConvertTo-Json
            Invoke-RestMethod -Uri "$BaseUrl/api/printers/status" -Method Post -Body $postBody -ContentType "application/json" -ErrorAction SilentlyContinue | Out-Null
            $LastStatusUpdate = Get-Date
        }

        if ($jobs -and $jobs.Count -gt 0) {
            foreach ($job in $jobs) {
                $targetIp = ""
                if ($job.printer_type -eq "kitchen") {
                    $targetIp = $printers.kitchen
                } elseif ($job.printer_type -eq "shisha") {
                    $targetIp = $printers.shisha
                } else {
                    $targetIp = $printers.cashier
                }

                if ([string]::IsNullOrWhiteSpace($targetIp)) {
                    $targetIp = $printers.cashier
                }

                if ([string]::IsNullOrWhiteSpace($targetIp)) {
                    Log-Message "Skip job #$($job.id) - no IP for $($job.printer_type)" "WARNING"
                    Invoke-RestMethod -Uri "$BaseUrl/api/print-jobs/$($job.id)/complete?token=$ApiToken" -Method Get -ErrorAction SilentlyContinue | Out-Null
                    continue
                }

                Log-Message "Printing job #$($job.id) [$($job.printer_type)] to $targetIp`:$port" "INFO"

                $maxRetries = 3
                $retryCount = 0
                $printed    = $false

                while ((-not $printed) -and ($retryCount -lt $maxRetries)) {
                    try {
                        $bytes  = [System.Convert]::FromBase64String($job.receipt_data)
                        $tcp    = New-Object System.Net.Sockets.TcpClient
                        $tcp.Connect($targetIp, $port)
                        $stream = $tcp.GetStream()
                        $stream.Write($bytes, 0, $bytes.Length)
                        $stream.Flush()
                        $stream.Close()
                        $tcp.Close()
                        Log-Message "SUCCESS job #$($job.id) on $targetIp" "SUCCESS"
                        Invoke-RestMethod -Uri "$BaseUrl/api/print-jobs/$($job.id)/complete?token=$ApiToken" -Method Get -ErrorAction SilentlyContinue | Out-Null
                        $printed = $true
                    } catch {
                        $retryCount++
                        Log-Message "FAIL attempt $retryCount/$maxRetries on $targetIp - $($_.Exception.Message)" "ERROR"
                        if ($retryCount -lt $maxRetries) {
                            Start-Sleep -Seconds 3
                        }
                    }
                }

                if (-not $printed) {
                    Log-Message "SKIP job #$($job.id) after $maxRetries failed attempts" "ERROR"
                    Invoke-RestMethod -Uri "$BaseUrl/api/print-jobs/$($job.id)/complete?token=$ApiToken" -Method Get -ErrorAction SilentlyContinue | Out-Null
                }
            }
        }
    } catch {
        # ignore connection errors silently
    }

    Start-Sleep -Seconds 2
}
