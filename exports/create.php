<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: ../index.php"); exit; }
require_once '../includes/db.php';

// Kaliya soo qaad xoolaha caafimaadkoodu yahay 'Healthy'
$animals = $pdo->query("SELECT id, animal_id FROM animals WHERE status = 'Healthy'")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $permit_number = "EXP-" . date('Ymd') . "-" . rand(10, 99);
    $animal_id = $_POST['animal_id'];
    $destination = trim($_POST['destination']);
    $officer_id = $_SESSION['user_id'];
    $issue_date = date('Y-m-d');

    $stmt = $pdo->prepare("INSERT INTO export_permits (permit_number, animal_id, officer_id, destination_country, status, issue_date) VALUES (?, ?, ?, ?, 'Approved', ?)");
    $stmt->execute([$permit_number, $animal_id, $officer_id, $destination, $issue_date]);

    // U badal statuska neefka 'Exported'
    $stmtUpdate = $pdo->prepare("UPDATE animals SET status = 'Exported' WHERE id = ?");
    $stmtUpdate->execute([$animal_id]);

    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="so">
<head>
    <meta charset="UTF-8">
    <title>New Export Permit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-5">

<div class="container" style="max-width: 500px;">
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <h4 class="mb-4">Generate Export Permit</h4>
            
            <form action="" method="POST">
                <div class="mb-3">
                    <label class="form-label">Select Approved Healthy Animal</label>
                    <select name="animal_id" class="form-select" required>
                        <option value="">-- Dooro Neef Caafimaad Qaba --</option>
                        <?php foreach($animals as $anm): ?>
                            <option value="<?= $anm['id']; ?>">#<?= $anm['animal_id']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label">Destination Country (Waddanka Loo Dhoofinayo)</label>
                    <select name="destination" class="form-select" required>
                        <option value="Saudi Arabia">Saudi Arabia</option>
                        <option value="UAE">UAE</option>
                        <option value="Oman">Oman</option>
                        <option value="Yemen">Yemen</option>
                    </select>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="index.php" class="btn btn-light">Back</a>
                    <button type="submit" class="btn btn-success">Approve & Print</button>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>