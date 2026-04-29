<?php
echo "<h2>Test du driver PostgreSQL PDO</h2>";

if (extension_loaded('pdo_pgsql')) {
    echo "✅ Driver PDO PostgreSQL : <strong>OK</strong>";
} else {
    echo "❌ Driver toujours absent";
}

echo "<br><br>Drivers PDO disponibles :<br>";
print_r(PDO::getAvailableDrivers());
?>