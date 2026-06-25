# Background Remover

Run this local FastAPI service before generating student ID cards if you want AI background removal.

On Windows, double-click `start.bat`, or run:

```powershell
cd C:\clone-github-repo\BCCIV2\BCCI-PANTAS\background_remover
.\start.bat
```

The script creates a local `.bg_venv` folder and installs the Python packages there.

Laravel calls the URL configured by `BACKGROUND_REMOVER_URL`:

```text
http://127.0.0.1:8010/remove-bg
```

If the service is not running, Laravel falls back to its PHP white-background remover.
