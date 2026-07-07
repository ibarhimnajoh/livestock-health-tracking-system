<?php
session_start();
if (!isset($_SESSION['user_id'])) { exit; }
require_once '../includes/db.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $pdo->prepare("DELETE FROM owners WHERE id = ?");
    $stmt->execute([$id]);
}
header("Location: index.php");
exit;
?>