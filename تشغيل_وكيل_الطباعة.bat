@echo off
chcp 65001 > nul
title Thermal Print Agent - Running
echo.
echo  ============================================
echo   Thermal Print Agent is Running
echo   Do NOT close this window during operation
echo  ============================================
echo.
powershell.exe -NoProfile -ExecutionPolicy Bypass -File "%~dp0print-agent.ps1"
pause
