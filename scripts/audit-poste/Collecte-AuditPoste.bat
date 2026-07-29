@echo off
setlocal EnableExtensions
title Collecte audit poste - GPI COFINA
cd /d "%~dp0"

REM Double-clic = collecte + envoi
REM Dossier partage : .bat + .ps1 + config.json

if not exist "%~dp0Collecte-AuditPoste.ps1" goto :err_ps1
if not exist "%~dp0config.json" goto :err_cfg

echo Collecte en cours...
echo.

powershell.exe -NoProfile -ExecutionPolicy Bypass -File "%~dp0Collecte-AuditPoste.ps1"
set ERR=%ERRORLEVEL%

echo.
if %ERR% equ 0 goto :ok
echo Echec code %ERR%. Verifiez ApiKey dans config.json, le reseau et l URL.
goto :end

:ok
echo Termine avec succes.
goto :end

:err_ps1
echo [ERREUR] Collecte-AuditPoste.ps1 introuvable dans ce dossier :
echo %~dp0
goto :end

:err_cfg
echo [ERREUR] config.json introuvable.
echo Copiez config.prod.example.json vers config.json puis renseignez ApiKey.
goto :end

:end
echo.
pause
exit /b %ERR%
