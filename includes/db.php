<?php

// Dejinta xogta lagu xirayo database-ka.
$host = "localhost";
$dbname = "livestock_system";
$username = "root";
$password = ""; // Haddii MySQL leeyahay password, halkan ku qor.

// Isku day in la sameeyo xiriirka database-ka.
try {

    // Abuurista PDO Database Connection.
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);

    // Soo bandhig qaladka haddii uu dhaco.
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Xogta kasoo baxda database-ka ha noqoto Associative Array.
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {

    // Haddii xiriirku fashilmo, muuji fariinta qaladka.
    die("Database Connection Failed: " . $e->getMessage());
}

?>