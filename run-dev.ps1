$ErrorActionPreference = 'Stop'

$projectRoot = Split-Path -Parent $MyInvocation.MyCommand.Path
$frontendRoot = Join-Path $projectRoot 'cse_reviewer'
$backendRoot = Join-Path $frontendRoot 'server'

Write-Host 'Checking MongoDB on 127.0.0.1:27017...' -ForegroundColor Cyan
if (-not (Test-NetConnection -ComputerName 127.0.0.1 -Port 27017 -InformationLevel Quiet)) {
    Write-Host 'MongoDB is not reachable on 127.0.0.1:27017.' -ForegroundColor Yellow
    Write-Host 'Start MongoDB first, then run this script again.' -ForegroundColor Yellow
    exit 1
}

if (-not (Test-Path (Join-Path $backendRoot 'node_modules'))) {
    Write-Host 'Installing backend dependencies...' -ForegroundColor Cyan
    Push-Location $backendRoot
    npm install
    Pop-Location
}

if (-not (Test-Path (Join-Path $frontendRoot 'node_modules'))) {
    Write-Host 'Installing frontend dependencies...' -ForegroundColor Cyan
    Push-Location $frontendRoot
    npm install
    Pop-Location
}

Write-Host 'Starting backend on http://localhost:5000...' -ForegroundColor Green
Start-Process powershell -ArgumentList @(
    '-NoExit',
    '-Command',
    "Set-Location '$backendRoot'; npm start"
)

Write-Host 'Starting frontend on http://localhost:3000...' -ForegroundColor Green
Start-Process powershell -ArgumentList @(
    '-NoExit',
    '-Command',
    "Set-Location '$frontendRoot'; npm start"
)

Write-Host 'Both processes launched. Keep MongoDB running in the background.' -ForegroundColor Green
