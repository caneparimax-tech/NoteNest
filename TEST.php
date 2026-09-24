<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Database Schema</title>
<style>
    body { font-family: Arial, sans-serif; }
    table { width: 100%; border-collapse: collapse; }
    th, td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }
    th { background-color: #f2f2f2; }
</style>
</head>
<body>
<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$pdo = new PDO('mysql:host=localhost;dbname=mjcanepa', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$query = "
    SELECT TABLE_NAME, COLUMN_NAME, IS_NULLABLE, DATA_TYPE, COLUMN_KEY, EXTRA, CHARACTER_MAXIMUM_LENGTH 
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = 'mjcanepa' 
    ORDER BY TABLE_NAME, ORDINAL_POSITION;
";

// Prepare and execute the query
$stmt = $pdo->prepare($query);
$stmt->execute();
$schema = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "<table>";
echo "<tr><th>Table</th><th>Column</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";

$currentTable = '';

foreach ($schema as $column) {
    if ($column['TABLE_NAME'] !== $currentTable) {
        $currentTable = $column['TABLE_NAME'];
        // New table row for the table name
        echo "<tr><td colspan='7'><strong>$currentTable</strong></td></tr>";
    }
    // Table row for each column
    echo "<tr>";
    echo "<td></td>"; // Empty cell for table name since it's already displayed
    echo "<td>{$column['COLUMN_NAME']}</td>";
    echo "<td>{$column['DATA_TYPE']}</td>";
    echo "<td>{$column['IS_NULLABLE']}</td>";
    echo "<td>{$column['COLUMN_KEY']}</td>";
    echo "<td>{$column['COLUMN_DEFAULT']}</td>";
    echo "<td>{$column['EXTRA']}</td>";
    echo "</tr>";
}

echo "</table>";
?>
</body>
</html>