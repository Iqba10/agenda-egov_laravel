<?php

$targetHost = 'tokaido.proxy.rlwy.net';
$targetPort = '21969';
$targetUser = 'root';
$targetPass = 'muFzwhbnRaWwkQcPNbgwQfCgNQyrXXli';
$targetDb = 'railway';

$backupDir = __DIR__ . '/backups';
$files = glob($backupDir . '/railway_backup_*.sql');
usort($files, function($a, $b) {
    return filemtime($b) - filemtime($a);
});
$backupFile = $files[0];

echo "DIRECT SQL RESTORE\n";
echo "Target: $targetHost:$targetPort/$targetDb\n";
echo "File: $backupFile\n\n";

$targetDsn = "mysql:host=$targetHost;port=$targetPort;dbname=$targetDb;charset=utf8mb4";
$targetPdo = new PDO($targetDsn, $targetUser, $targetPass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

// Drop all tables
$tables = $targetPdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
$targetPdo->exec("SET FOREIGN_KEY_CHECKS = 0");
foreach ($tables as $table) {
    $targetPdo->exec("DROP TABLE IF EXISTS `$table`");
}

// Read and execute SQL
$sql = file_get_contents($backupFile);
$targetPdo->exec($sql);
$targetPdo->exec("SET FOREIGN_KEY_CHECKS = 1");

echo "DONE\n";

$restoredTables = $targetPdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
foreach ($restoredTables as $table) {
    $count = $targetPdo->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
    echo "$table: $count rows\n";
}
