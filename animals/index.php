<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: ../index.php"); exit; }
require_once '../includes/db.php';

// Soo qaad doorka qofka nidaamka soo galay
$role = $_SESSION['role'] ?? 'Export Officer';

// Soo qaad xoolaha iyo magaca milkiilahooda (JOIN Query)
$query = "SELECT a.*, o.name as owner_name FROM animals a JOIN owners o ON a.owner_id = o.id ORDER BY a.id DESC";
$animals = $pdo->query($query)->fetchAll();
?>
<!DOCTYPE html>
<html lang="so">
<head>
    <meta charset="UTF-8">
    <title>Animal Records - VetExpert</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="../assets/js/main.js" defer></script>
</head>
<body class="bg-light">

<div class="container-fluid">
    <div class="row">
        <div class="col-md-2 bg-dark min-vh-100 p-3 text-white">
            <h5>VetExpert</h5>
            <hr>
            <a href="../dashboard/dashboard.php" class="text-white d-block mb-2 text-decoration-none"><i class="bi bi-speedometer2"></i> Dashboard</a>
            <a href="../owners/index.php" class="text-white d-block mb-2 text-decoration-none"><i class="bi bi-people"></i> Owners</a>
            <a href="index.php" class="text-success d-block mb-2 text-decoration-none"><i class="bi bi-cow"></i> Animals</a>
        </div>

        <div class="col-md-10 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4>Animal Records</h4>
                <?php if ($role == 'Admin' || $role == 'Veterinary Officer'): ?>
                    <a href="register.php" class="btn btn-success"><i class="bi bi-plus-lg"></i> Register New Animal</a>
                <?php endif; ?>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Animal ID (RFID)</th>
                                <th>Species</th>
                                <th>Owner / Farm</th>
                                <th>Status</th>
                                <?php if ($role == 'Admin' || $role == 'Veterinary Officer'): ?>
                                    <th class="text-end px-4">Actions</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($animals as $animal): 
                                $badge = 'bg-success';
                                if($animal['status'] == 'Quarantined') $badge = 'bg-warning text-dark';
                                if($animal['status'] == 'Treatment') $badge = 'bg-danger';
                                if($animal['status'] == 'Exported') $badge = 'bg-info text-dark';
                            ?>
                            <tr>
                                <td class="fw-bold text-primary">#<?= htmlspecialchars($animal['animal_id']); ?></td>
                                <td><?= htmlspecialchars($animal['species']) . " (" . htmlspecialchars($animal['breed']) . ")"; ?></td>
                                <td><?= htmlspecialchars($animal['owner_name']); ?></td>
                                <td><span class="badge <?= $badge; ?>"><?= $animal['status']; ?></span></td>
                                
                                <?php if ($role == 'Admin' || $role == 'Veterinary Officer'): ?>
                                    <td class="text-end px-4">
                                        <a href="../health/add_record.php?animal_id=<?= $animal['id']; ?>" class="btn btn-sm btn-outline-secondary" title="Check Health">
                                            <i class="bi bi-heart-pulse"></i>
                                        </a>
                                        <a href="edit.php?id=<?= $animal['id']; ?>" class="btn btn-sm btn-outline-primary" title="Edit Profile">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    </td>
                                <?php endif; ?>
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