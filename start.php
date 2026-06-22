<?php
/**
 * Cross-platform Application Starter
 * Spawns both the built-in PHP server and the Node HTTPS proxy.
 */

// Force output buffering flush
ob_implicit_flush(true);

$phpCmd = '"' . PHP_BINARY . '" -S localhost:8000 router.php';
$nodeCmd = "node proxy.js";

function open_browser($url) {
    $os = strtoupper(substr(PHP_OS, 0, 3));
    if ($os === 'WIN') {
        pclose(popen("start " . $url, "r"));
    } elseif ($os === 'DAR') {
        pclose(popen("open " . $url, "r"));
    } else {
        pclose(popen("xdg-open " . $url . " >/dev/null 2>&1 &", "r"));
    }
}

echo "========================================\n";
echo "  IdentityHub PHP Sample Startup\n";
echo "========================================\n\n";

$descriptors = [
    0 => ["pipe", "r"], // stdin
    1 => ["pipe", "w"], // stdout
    2 => ["pipe", "w"]  // stderr
];

echo "1. Starting PHP Server on http://localhost:8000...\n";
$phpProcess = proc_open($phpCmd, $descriptors, $phpPipes);

echo "2. Starting HTTPS Proxy on https://localhost:7284...\n";
$nodeProcess = proc_open($nodeCmd, $descriptors, $nodePipes);

if (!is_resource($phpProcess) || !is_resource($nodeProcess)) {
    echo "Error: Failed to start processes.\n";
    exit(1);
}

// Make pipes non-blocking so we can read them dynamically
stream_set_blocking($phpPipes[1], 0);
stream_set_blocking($phpPipes[2], 0);
stream_set_blocking($nodePipes[1], 0);
stream_set_blocking($nodePipes[2], 0);

echo "\n✓ Secure Application is running at https://localhost:7284\n";
echo "Opening default browser...\n";
open_browser("https://localhost:7284");
echo "Press Ctrl+C to stop all servers...\n\n";

// Register shutdown cleanup to terminate child processes when this script exits
register_shutdown_function(function() use ($phpProcess, $nodeProcess) {
    echo "\nStopping servers...\n";
    @proc_terminate($phpProcess);
    @proc_terminate($nodeProcess);
    echo "All servers stopped.\n";
});

// Run loop to pipe outputs and check process health
while (true) {
    $phpStatus = proc_get_status($phpProcess);
    $nodeStatus = proc_get_status($nodeProcess);
    
    if (!$phpStatus['running']) {
        echo "\n[Error] PHP Server stopped unexpectedly.\n";
        break;
    }
    if (!$nodeStatus['running']) {
        echo "\n[Error] HTTPS Proxy stopped unexpectedly.\n";
        break;
    }

    // Read and print any stdout/stderr outputs
    while ($out = fgets($phpPipes[1])) {
        echo "[PHP] " . trim($out) . "\n";
    }
    while ($err = fgets($phpPipes[2])) {
        echo "[PHP ERR] " . trim($err) . "\n";
    }
    while ($out = fgets($nodePipes[1])) {
        echo "[Proxy] " . trim($out) . "\n";
    }
    while ($err = fgets($nodePipes[2])) {
        echo "[Proxy ERR] " . trim($err) . "\n";
    }

    usleep(100000); // Sleep for 100ms
}
