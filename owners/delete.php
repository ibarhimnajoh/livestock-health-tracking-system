<?php
session_start();
require_once '../includes/db.php';

// Amniga doorka: Kaliya Admin iyo Vet ayaa tirtiri kara
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'Admin' && $_SESSION['role'] !== 'Veterinary Officer')) {
    die("Codsigaaga waa la diiday. Awood uma lihid inaad tirtirto.");
}

$id = $_GET['id'] ?? null;

if ($id) {
    // FIIRO GAAR AH: Haddii database-kaagu leeyahay Foreign Key (ON DELETE CASCADE), 
    // xoolaha uu leeyahayna si toos ah ayay u tirtirmayaan.
    $stmt = $pdo->prepare("DELETE FROM owners WHERE id = ?");
    $stmt->execute([$id]);
}

header("Location: index.php?deleted=true");
exit;