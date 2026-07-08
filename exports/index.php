

<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: ../index.php"); exit; }
require_once '../includes/db.php';

$query = "SELECT p.*, a.animal_id as rfid, o.name as owner_name 
          FROM export_permits p 
          JOIN animals a ON p.animal_id = a.id 
          JOIN owners o ON a.owner_id = o.id 
          ORDER BY p.id DESC";
$permits = $pdo->query($query)->fetchAll();
?>
<!DOCTYPE html>
<html lang="so">
<head>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
<link rel="stylesheet" href="../assets/css/style.css">
<script src="../assets/js/main.js" defer></script>

    <meta charset="UTF-8">
    <title>Export Permits</title>
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
            <a href="index.php" class="text-success d-block mb-2 text-decoration-none"><i class="bi bi-file-earmark-pdf"></i> Export Permits</a>
            <a href="create.php" class="text-white d-block mb-2 text-decoration-none"><i class="bi bi-plus-circle"></i> Issue New Permit</a>
        </div>

        <div class="col-md-10 p-4">
            <h4 class="mb-4">Export Permit Declarations</h4>

            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Permit No</th>
                                <th>Animal ID</th>
                                <th>Exporter Name</th>
                                <th>Destination</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($permits as $p): ?>
                            <tr>
                                <td class="fw-bold"><?= $p['permit_number']; ?></td>
                                <td>#<?= htmlspecialchars($p['rfid']); ?></td>
                                <td><?= htmlspecialchars($p['owner_name']); ?></td>
                                <td><?= htmlspecialchars($p['destination_country']); ?></td>
                                <td>
                                    <span class="badge <?= $p['status'] == 'Approved' ? 'bg-success' : 'bg-warning text-dark'; ?>">
                                        <?= $p['status']; ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="download_pdf.php?id=<?= $p['id']; ?>" class="btn btn-sm btn-danger" target="_blank">
                                        <i class="bi bi-file-pdf"></i> PDF
                                    </a>
                                </td>
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