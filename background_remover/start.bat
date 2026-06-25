@echo off
setlocal
cd /d "%~dp0"

set "HOST=127.0.0.1"
set "PORT=8010"
set "VENV_DIR=.bg_venv"
set "PYTHON=%VENV_DIR%\Scripts\python.exe"
set "TMP=%CD%\.tmp"
set "TEMP=%TMP%"

if not exist "%TMP%" mkdir "%TMP%"

call :ensure_venv
if errorlevel 1 goto failed

echo Checking Background Remover dependencies...
"%PYTHON%" -c "import fastapi, uvicorn, multipart, rembg, onnxruntime, PIL, numpy" >nul 2>nul
if errorlevel 1 (
    echo Installing Background Remover dependencies...
    "%PYTHON%" -m pip install --upgrade pip
    if errorlevel 1 goto failed
    "%PYTHON%" -m pip install -r requirements.txt
    if errorlevel 1 goto failed
)

echo Starting Background Remover API at http://%HOST%:%PORT%
"%PYTHON%" -m uvicorn app:app --host %HOST% --port %PORT%
goto finished

:failed
echo Failed to start Background Remover API. Check that Python is installed and available in PATH.

:finished
pause
endlocal
exit /b

:ensure_venv
if exist "%PYTHON%" (
    "%PYTHON%" -m pip --version >nul 2>nul
    if not errorlevel 1 exit /b 0

    echo Existing Python virtual environment is incomplete. Recreating it...
    rmdir /s /q "%VENV_DIR%"
)

echo Creating Python virtual environment...
python -m venv "%VENV_DIR%"
if not errorlevel 1 goto verify_venv

echo Retrying with the Python launcher...
py -3 -m venv "%VENV_DIR%"
if errorlevel 1 exit /b 1

:verify_venv
"%PYTHON%" -m pip --version >nul 2>nul
if errorlevel 1 exit /b 1

exit /b 0
