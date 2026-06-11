# Start the IdentityHub PHP Sample Application with SSL
Write-Host '========================================' -ForegroundColor Cyan
Write-Host '  IdentityHub PHP Sample - SSL Startup' -ForegroundColor Cyan
Write-Host '========================================' -ForegroundColor Cyan
Write-Host ''

$PHP_EXE = 'D:\PHP Web App\tools\php\php.exe'
$PHP_PORT = 8000
$SSL_PORT = 7284

if (Test-Path $PHP_EXE) {
    Write-Host '1. Starting PHP Server on http://localhost:8000...' -ForegroundColor Green
    $phpJob = Start-Job -ScriptBlock {
        param($path, $port)
        cd 'D:\PHP Web App'
        & $path -S localhost:$port router.php
    } -ArgumentList $PHP_EXE, $PHP_PORT

    Write-Host '2. Starting NodeJS HTTPS Proxy on https://localhost:7284...' -ForegroundColor Cyan
    $proxyJob = Start-Job -ScriptBlock {
        cd 'D:\PHP Web App'
        node proxy.js
    }

    Write-Host '3. Launching browser...' -ForegroundColor Yellow
    Start-Sleep -Seconds 3
    Start-Process 'https://localhost:7284'
    
    Write-Host ''
    Write-Host '✓ Application is running at https://localhost:7284' -ForegroundColor Green
    Write-Host 'Press Enter to stop all servers...' -ForegroundColor White
    Read-Host
    
    Stop-Job -Job $phpJob
    Stop-Job -Job $proxyJob
    Write-Host 'Servers stopped.' -ForegroundColor Red
} else {
    Write-Host 'Error: PHP executable not found.' -ForegroundColor Red
    Write-Host ''
    Write-Host '========================================' -ForegroundColor Yellow
    Write-Host '  PHP Installation Required' -ForegroundColor Yellow
    Write-Host '========================================' -ForegroundColor Yellow
    Write-Host ''
    Write-Host 'This application requires PHP to run. Please install PHP using one of the following methods:' -ForegroundColor White
    Write-Host ''
    Write-Host '1. Manual Installation (Recommended):' -ForegroundColor Cyan
    Write-Host '   a. Download PHP from: https://windows.php.net/download/' -ForegroundColor White
    Write-Host '   b. Choose "VS16 x64 Thread Safe" ZIP version' -ForegroundColor White
    Write-Host '   c. Extract to: D:\PHP Web App\tools\php\' -ForegroundColor White
    Write-Host '   d. Ensure php.exe is at: D:\PHP Web App\tools\php\php.exe' -ForegroundColor White
    Write-Host ''
    Write-Host '2. Install via Chocolatey:' -ForegroundColor Cyan
    Write-Host '   choco install php' -ForegroundColor White
    Write-Host '   Then update $PHP_EXE variable in this script to point to the installed location' -ForegroundColor Gray
    Write-Host ''
    Write-Host '3. Install via Scoop:' -ForegroundColor Cyan
    Write-Host '   scoop install php' -ForegroundColor White
    Write-Host '   Then update $PHP_EXE variable in this script to point to the installed location' -ForegroundColor Gray
    Write-Host ''
    Write-Host 'After installation, run this script again.' -ForegroundColor Green
    Write-Host ''
}
