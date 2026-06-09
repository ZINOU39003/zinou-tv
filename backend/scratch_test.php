<?php
$logPath = __DIR__ . '/storage/logs/laravel.log';
if (file_exists($logPath)) {
    $lines = file($logPath);
    $lastLines = array_slice($lines, -600); // Check last 600 lines
    echo "Recent exceptions:\n";
    $print = false;
    $printedCount = 0;
    foreach ($lastLines as $line) {
        if (preg_match('/^\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\]/', $line)) {
            // Check if this log entry contains ERROR or exception
            if (str_contains($line, '.ERROR') || str_contains($line, 'exception') || str_contains($line, 'Proxy exception')) {
                $print = true;
                $printedCount++;
                echo "\n----------------------------------------\n";
                echo $line;
            } else {
                $print = false;
            }
        } elseif ($print) {
            // Print the next 10 lines of stack trace
            echo $line;
        }
    }
    echo "\nTotal exceptions printed: $printedCount\n";
} else {
    echo "Log file not found.\n";
}
