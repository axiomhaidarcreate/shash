# ====================================================
#   وكيل الطباعة المحلي الاحترافي (Cloud Print Agent V2)
#   الميزات: طابور، إعادة محاولة تلقائية، طابعات متعددة، تقارير حالة، سجلات
# ====================================================

# 1. إعدادات السيرفر
$BaseUrl = "https://app.marsksa.com" # استبدلها برابط موقعك
$ApiToken = "QZn8O5eJN4zj91RcRyIhSeKaSZT5oWjn"          # استبدله بالرمز الموجود في لوحة الإعدادات

# مسارات السجلات
$LogFile = "printing.log"

function Log-Message {
    param([string]$Message, [string]$Type="INFO")
    $Timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
    $LogEntry = "[$Timestamp] [$Type] $Message"
    Write-Host $LogEntry
    Add-Content -Path $LogFile -Value $LogEntry
}

Log-Message "بدء تشغيل وكيل الطباعة المحلي..." "START"

function Test-PrinterConnection {
    param([string]$Ip, [int]$Port)
    if ([string]::IsNullOrWhiteSpace($Ip)) { return "Not Configured" }
    try {
        $tcp = New-Object System.Net.Sockets.TcpClient
        $result = $tcp.BeginConnect($Ip, $Port, $null, $null)
        $success = $result.AsyncWaitHandle.WaitOne([timespan]::FromSeconds(1))
        if ($success) {
            $tcp.EndConnect($result)
            $tcp.Close()
            return "Online"
        }
        return "Offline"
    } catch {
        return "Error"
    }
}

$LastStatusUpdate = (Get-Date).AddMinutes(-5)

while ($true) {
    try {
        # جلب الطلبات
        $response = Invoke-RestMethod -Uri "$BaseUrl/api/print-jobs?token=$ApiToken" -Method Get -ErrorAction Stop
        
        $jobs = $response.jobs
        $printers = $response.printers
        $port = $response.port

        # تحديث حالة الطابعات كل دقيقة
        if ((Get-Date) -gt $LastStatusUpdate.AddMinutes(1)) {
            $statuses = @{
                "cashier" = Test-PrinterConnection -Ip $printers.cashier -Port $port
                "kitchen" = Test-PrinterConnection -Ip $printers.kitchen -Port $port
                "shisha" = Test-PrinterConnection -Ip $printers.shisha -Port $port
            }
            
            $body = @{ token = $ApiToken; statuses = $statuses } | ConvertTo-Json
            Invoke-RestMethod -Uri "$BaseUrl/api/printers/status" -Method Post -Body $body -ContentType "application/json" -ErrorAction SilentlyContinue | Out-Null
            
            $LastStatusUpdate = Get-Date
        }

        if ($jobs -and $jobs.Count -gt 0) {
            foreach ($job in $jobs) {
                $targetIp = $printers.($job.printer_type)
                
                if ([string]::IsNullOrWhiteSpace($targetIp)) {
                    Log-Message "تخطي الفاتورة $($job.id) - لم يتم تحديد IP لطابعة $($job.printer_type)." "WARNING"
                    continue
                }

                Log-Message "جاري طباعة الفاتورة $($job.id) على طابعة $($job.printer_type) ($targetIp)..."

                $retryCount = 0
                $printed = $false

                while (-not $printed) {
                    try {
                        $bytes = [System.Convert]::FromBase64String($job.receipt_data)
                        $tcp = New-Object System.Net.Sockets.TcpClient
                        $tcp.Connect($targetIp, $port)
                        $stream = $tcp.GetStream()
                        $stream.Write($bytes, 0, $bytes.Length)
                        $stream.Close()
                        $tcp.Close()
                        
                        Log-Message "نجحت الطباعة ($targetIp)." "SUCCESS"
                        
                        Invoke-RestMethod -Uri "$BaseUrl/api/print-jobs/$($job.id)/complete?token=$ApiToken" -Method Get -ErrorAction SilentlyContinue | Out-Null
                        $printed = $true
                    } catch {
                        $retryCount++
                        Log-Message "فشل الطباعة على $targetIp (محاولة $retryCount). إعادة المحاولة بعد 5 ثوانٍ..." "ERROR"
                        Start-Sleep -Seconds 5
                    }
                }
            }
        }
    } catch {
        # صمت أخطاء السيرفر (مثل عدم توفر الإنترنت)
    }

    Start-Sleep -Seconds 2
}
