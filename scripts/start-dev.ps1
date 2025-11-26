param([int]$Port=8000)
Set-Location (Split-Path $PSScriptRoot -Parent)
$php = Start-Process powershell -ArgumentList "-NoExit","-Command","php artisan serve --host=localhost --port=$Port" -PassThru -WorkingDirectory (Split-Path $PSScriptRoot -Parent)
$vite = Start-Process powershell -ArgumentList "-NoExit","-Command","npm run dev" -PassThru -WorkingDirectory (Split-Path $PSScriptRoot -Parent)
Write-Output "Backend http://localhost:$Port"
Write-Output "Frontend http://localhost:5173"
Write-Output "Pressione Enter para encerrar servidores"
[void](Read-Host)
try { Stop-Process -Id $php.Id -Force } catch {}
try { Stop-Process -Id $vite.Id -Force } catch {}
