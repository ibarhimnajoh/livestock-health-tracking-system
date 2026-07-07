<?php
session_start();
if (!isset($_SESSION['user_id'])) { 
    header("Location: ../index.php"); 
    exit; 
}
require_once '../includes/db.php';

$role = $_SESSION['role'] ?? 'Export Officer';

// Waxaan u beddelnay 'vaccinations' iyo khaanadda 'vaccinated_at' si ay ugu ekaato sawirkaaga dhabta ah
$query = "SELECT v.id, v.vaccine_name, v.batch_number, v.vaccinated_at, v.expiry_date, a.animal_id AS rfid, a.species 
          FROM vaccinations v
          JOIN animals a ON v.animal_id = a.id
          ORDER BY v.id DESC";
$vaccinations = $pdo->query($query)->fetchAll();
?>
<!DOCTYPE html>
<html lang="so">
<head>
    <meta charset="UTF-8">
    <title>Vaccination Records - VetExpert</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-light">

<div class="container-fluid">
    <div class="row">
        <div class="col-md-2 bg-dark min-vh-100 p-3 text-white">
            <h5>VetExpert</h5>
            <hr>
            <a href="../dashboard/dashboard.php" class="text-white d-block mb-2 text-decoration-none"><i class="bi bi-speedometer2"></i> Dashboard</a>
            <a href="../owners/index.php" class="text-white d-block mb-2 text-decoration-none"><i class="bi bi-people"></i> Owners</a>
            <a href="../animals/index.php" class="text-white d-block mb-2 text-decoration-none"><i class="bi bi-cow"></i> Animals</a>
            <a href="index.php" class="text-success d-block mb-2 text-decoration-none"><i class="bi bi-shield-check"></i> Vaccinations</a>
        </div>

        <div class="col-md-10 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4>Vaccination Logs</h4>
                <?php if ($role == 'Admin' || $role == 'Veterinary Officer'): ?>
                    <a href="add.php" class="btn btn-success"><i class="bi bi-plus-lg"></i> Record New Vaccine</a>
                <?php endif; ?>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Animal ID (RFID)</th>
                                <th>Species</th>
                                <th>Vaccine Name</th>
                                <th>Batch Number</th>
                                <th>Vaccinated At</th>
                                <th>Expiry Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($vaccinations) > 0): ?>
                                <?php foreach ($vaccinations as $vac): ?>
                                <tr>
                                    <td class="fw-bold text-primary">#<?= htmlspecialchars($vac['rfid']); ?></td>
                                    <td><?= htmlspecialchars($vac['species']); ?></td>
                                    <td class="fw-bold text-dark"><?= htmlspecialchars($vac['vaccine_name']); ?></td>
                                    <td><code class="text-secondary"><?= htmlspecialchars($vac['batch_number']); ?></code></td>
                                    <td><?= htmlspecialchars($vac['vaccinated_at']); ?></td>
                                    <td><span class="badge bg-warning text-dark"><?= htmlspecialchars($vac['expiry_date']); ?></span></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="6" class="text-center text-muted py-4">Lama helin wax diiwaan tallaal ah.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>