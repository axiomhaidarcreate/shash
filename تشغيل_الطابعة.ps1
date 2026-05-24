
# ====================================================
#   سكريبت تفعيل طابعة POSSTORE PS-7
#   IP: 192.168.8.189 | Port: 9100
# ====================================================

$PrinterIP   = "192.168.8.189"
$PrinterPort = 9100
$PrinterName = "POSSTORE PS-7"
$PortName    = "POSSTORE_PS7_Port"

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "   فحص طابعة POSSTORE PS-7" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# 1. فحص الاتصال بالشبكة
Write-Host ">> فحص الاتصال بالطابعة على $PrinterIP ..." -ForegroundColor Yellow
$ping = Test-Connection -ComputerName $PrinterIP -Count 2 -Quiet

if ($ping) {
    Write-Host "   [OK] الطابعة متصلة بالشبكة!" -ForegroundColor Green
} else {
    Write-Host "   [خطأ] الطابعة غير متصلة! تأكد من:" -ForegroundColor Red
    Write-Host "         - كيبل الإيثرنت موصول بالسويتش" -ForegroundColor Red
    Write-Host "         - الطابعة شغالة" -ForegroundColor Red
    Write-Host "         - جهازك على نفس الشبكة 192.168.8.x" -ForegroundColor Red
    Read-Host "اضغط Enter للخروج"
    exit
}

# 2. فحص البورت 9100
Write-Host ""
Write-Host ">> فحص منفذ الطباعة $PrinterPort ..." -ForegroundColor Yellow
try {
    $tcp = New-Object System.Net.Sockets.TcpClient
    $tcp.Connect($PrinterIP, $PrinterPort)
    $tcp.Close()
    Write-Host "   [OK] منفذ $PrinterPort مفتوح وجاهز!" -ForegroundColor Green
} catch {
    Write-Host "   [تحذير] المنفذ $PrinterPort غير متاح حالياً" -ForegroundColor DarkYellow
}

# 3. التحقق من البورت موجود
Write-Host ""
Write-Host ">> التحقق من إعدادات البورت ..." -ForegroundColor Yellow
$existingPort = Get-PrinterPort -Name $PortName -ErrorAction SilentlyContinue
if (-not $existingPort) {
    Write-Host "   إضافة بورت الطابعة ..." -ForegroundColor Yellow
    Add-PrinterPort -Name $PortName -PrinterHostAddress $PrinterIP -PortNumber $PrinterPort
    Write-Host "   [OK] تم إضافة البورت" -ForegroundColor Green
} else {
    Write-Host "   [OK] البورت موجود مسبقاً" -ForegroundColor Green
}

# 4. التحقق من الطابعة مضافة
Write-Host ""
Write-Host ">> التحقق من تثبيت الطابعة ..." -ForegroundColor Yellow
$existingPrinter = Get-Printer -Name $PrinterName -ErrorAction SilentlyContinue
if (-not $existingPrinter) {
    Write-Host "   إضافة الطابعة ..." -ForegroundColor Yellow
    Add-PrinterDriver -Name "Generic / Text Only" -ErrorAction SilentlyContinue
    Add-Printer -Name $PrinterName -DriverName "Generic / Text Only" -PortName $PortName
    Write-Host "   [OK] تم إضافة الطابعة" -ForegroundColor Green
} else {
    Write-Host "   [OK] الطابعة مثبتة مسبقاً" -ForegroundColor Green
}

# 5. تعيينها كطابعة افتراضية
Write-Host ""
Write-Host ">> تعيين الطابعة كافتراضية ..." -ForegroundColor Yellow
(New-Object -ComObject WScript.Network).SetDefaultPrinter($PrinterName)
Write-Host "   [OK] تم تعيين POSSTORE PS-7 كطابعة افتراضية!" -ForegroundColor Green

# 6. طباعة صفحة تجربة
Write-Host ""
$testPrint = Read-Host ">> هل تريد طباعة صفحة تجربة؟ (y/n)"
if ($testPrint -eq "y" -or $testPrint -eq "Y") {
    $testText = "*** اختبار الطباعة ***`nPOSSTORE PS-7`nIP: 192.168.8.189`n" + (Get-Date)
    $testText | Out-Printer -Name $PrinterName
    Write-Host "   [OK] تم إرسال صفحة الاختبار للطابعة!" -ForegroundColor Green
}

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "   الطابعة جاهزة للاستخدام!" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Read-Host "اضغط Enter للخروج"
