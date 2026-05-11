<?php
try {
    $serverName = "localhost\\SQLEXPRESS";
    $database   = "location_apppareil_electronique";
    $pdo = new PDO(
        "sqlsrv:Server=$serverName;Database=$database", "juck", "5507"
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>