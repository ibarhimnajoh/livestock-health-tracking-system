


<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: ../index.php"); exit; }
require_once '../includes/db.php';

// Soo qaad taariikhda baadhitaanada iyo aqoonsiga xoolaha
$query = "SELECT h.*, a.animal_id as rfid, u.fullname as vet_name 
          FROM health_records h 
          JOIN animals a ON h.animal_id = a.id 
          JOIN users u ON h.vet_id = u.id 
          ORDER BY h.id DESC";
$inspections = $pdo->query($query)->fetchAll();
?>
<!DOCTYPE html>
<html lang="so">
<head>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
<link rel="stylesheet" href="../assets/css/style.css">
<script src="../assets/js/main.js" defer></script>


    <meta charset="UTF-8">
    <title>Health Inspections - VetExpert</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">

<div class="container-fluid">
    <div class="row">
        <div class="col-md-2 bg-dark min-vh-100 p-3 text-white">
            <h5>VetExpert</h5>
            <hr>
            <a href="../dashboard/dashboard.php" class="text-white d-block mb-2 text-decoration-none"><i class="bi bi-speedometer2"></i> Dashboard</a>
            <a href="index.php" class="text-success d-block mb-2 text-decoration-none"><i class="bi bi-heart-pulse"></i> Health</a>
            <a href="../vaccinations/index.php" class="text-white d-block mb-2 text-decoration-none"><i class="bi bi-shield-plus"></i> Vaccinations</a>
        </div>

        <div class="col-md-10 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4>Health Inspections</h4>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Animal ID (RFID)</th>
                                <th>Diagnosis / Baadhistii</th>
                                <th>Status</th>
                                <th>Inspection Date</th>
                                <th>Inspected By</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($inspections as $ins): ?>
                            <tr>
                                <td class="fw-bold text-primary">#<?= htmlspecialchars($ins['rfid']); ?></td>
                                <td><?= htmlspecialchars($ins['diagnosis']); ?></td>
                                <td>
                                    <span class="badge <?= $ins['status'] == 'Healthy' ? 'bg-success' : 'bg-warning text-dark'; ?>">
                                        <?= $ins['status']; ?>
                                    </span>
                                </td>
                                <td><?= $ins['inspection_date']; ?></td>
                                <td><?= htmlspecialchars($ins['vet_name']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>