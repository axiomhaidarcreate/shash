' ====================================================
'   تشغيل وكيل الطباعة في الخلفية (بدون أي نافذة)
'   ضع هذا الملف في نفس مجلد وكيل_الطباعة_المحلي.ps1
' ====================================================

Dim objShell
Set objShell = CreateObject("WScript.Shell")

' مسار السكربت (في نفس مجلد هذا الملف)
Dim scriptPath
scriptPath = CreateObject("Scripting.FileSystemObject").GetParentFolderName(WScript.ScriptFullName)

' تشغيل PowerShell بشكل كامل مخفي (0 = مخفي تماماً)
objShell.Run "powershell.exe -ExecutionPolicy Bypass -WindowStyle Hidden -File """ & scriptPath & "\وكيل_الطباعة_المحلي.ps1""", 0, False

Set objShell = Nothing
