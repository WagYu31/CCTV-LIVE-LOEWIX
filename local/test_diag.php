<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
header('Content-Type: text/plain');
echo "Step 1: Starting test\n";
try {
    require_once __DIR__ . '/../config/db.php';
    echo "Step 2: DB file required successfully\n";
    $db = get_db_data();
    echo "Step 3: get_db_data() success! Total cameras: " . count($db['cameras'] ?? []) . "\n";
    foreach (($db['cameras'] ?? []) as $c) {
        echo " - Camera [{$c['id']}] {$c['title']}\n";
    }
} catch (Throwable $e) {
    echo "ERROR CAUGHT: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine() . "\n";
}
