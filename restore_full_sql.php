<?php

// Full SQL restore - direct approach
$targetHost = 'tokaido.proxy.rlwy.net';
$targetPort = '21969';
$targetUser = 'root';
$targetPass = 'muFzwhbnRaWwkQcPNbgwQfCgNQyrXXli';
$targetDb = 'railway';

// Find the latest backup file
$backupDir = __DIR__ . '/backups';
$files = glob($backupDir . '/railway_backup_*.sql');

if (empty($files)) {
    echo "Error: No backup files found in {$backupDir}\n";
    exit(1);
}

usort($files, function($a, $b) {
    return filemtime($b) - filemtime($a);
});

$backupFile = $files[0];

echo "Starting FULL SQL restore...\n";
echo "Target: $targetHost:$targetPort/$targetDb\n";
echo "Backup: $backupFile\n";
echo "File Size: " . filesize($backupFile) . " bytes\n\n";

try {
    $targetDsn = "mysql:host=$targetHost;port=$targetPort;dbname=$targetDb;charset=utf8mb4";
    $targetPdo = new PDO($targetDsn, $targetUser, $targetPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    echo "Connected to target database successfully!\n\n";

    // Drop all existing tables
    echo "Dropping all existing tables...\n";
    $tables = $targetPdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    $targetPdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    
    foreach ($tables as $table) {
        $targetPdo->exec("DROP TABLE IF EXISTS `$table`");
        echo "  - Dropped: $table\n";
    }
    
    echo "\nStarting SQL restore...\n\n";

    // Read backup file
    $sql = file_get_contents($backupFile);
    
    if ($sql === false) {
        echo "Error: Could not read backup file\n";
        exit(1);
    }

    // Split by semicolons - simple but effective approach
    $statements = explode(';', $sql);
    
    echo "Found " . count($statements) . " statements\n\n";

    $successCount = 0;
    $errorCount = 0;

    foreach ($statements as $index => $statement) {
        $statement = trim($statement);
        
        // Skip empty statements and comments
        if (empty($statement) || strpos($statement, '--') === 0) {
            continue;
        }
        
        // Skip dokumen_agenda INSERT with binary content
        if (strpos($statement, 'INSERT INTO `dokumen_agenda`') !== false && strpos($statement, '`content`') !== false) {
            echo "Skipping dokumen_agenda binary content\n";
            continue;
        }
        
        try {
            $targetPdo->exec($statement);
            $successCount++;
            
            if ($successCount % 50 === 0) {
                echo "Progress: $successCount statements executed...\r";
            }
        } catch (PDOException $e) {
            $errorCount++;
            echo "\nError at statement " . ($index + 1) . ": " . substr($e->getMessage(), 0, 100) . "\n";
            
            // Try to fix common syntax errors
            if (strpos($e->getMessage(), 'syntax error') !== false) {
                // Try removing problematic characters
                $fixedStatement = preg_replace('/[^\x20-\x7E\n\r\t]/', '', $statement);
                try {
                    $targetPdo->exec($fixedStatement);
                    $successCount++;
                    echo "Fixed and executed statement\n";
                } catch (PDOException $e2) {
                    echo "Could not fix: " . substr($e2->getMessage(), 0, 50) . "\n";
                }
            }
        }
    }

    $targetPdo->exec("SET FOREIGN_KEY_CHECKS = 1");

    echo "\n\nRestore completed!\n";
    echo "Successfully executed: $successCount statements\n";
    echo "Errors: $errorCount\n";

    // Verify tables
    echo "\nVerifying restored tables:\n";
    $restoredTables = $targetPdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "Restored " . count($restoredTables) . " tables:\n";
    foreach ($restoredTables as $table) {
        $count = $targetPdo->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
        echo "  - $table: $count rows\n";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
