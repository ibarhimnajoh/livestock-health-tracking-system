<?php
session_start();
if (!isset($_SESSION['user_id'])) { 
    header("Location: ../index.php"); 
    exit; 
}
require_once '../includes/db.php';

// Soo qaad doorka qofka nidaamka soo galay (Default: Export Officer haddii aan la helin)
$role = $_SESSION['role'] ?? 'Export Officer';

// Soo qaad dhamaan milkiilayaasha
$stmt = $pdo->query("SELECT * FROM owners ORDER BY id DESC");
$owners = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="so">
<head>
    <meta charset="UTF-8">
    <title>Livestock Owners - VetExpert</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="../assets/js/main.js" defer></script>
</head>
<body class="bg-light">

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar Menu -->
        <div class="col-md-2 bg-dark min-vh-100 p-3 text-white">
            <h5>VetExpert</h5>
            <hr>
            <a href="../dashboard/dashboard.php" class="text-white d-block mb-2 text-decoration-none"><i class="bi bi-speedometer2"></i> Dashboard</a>
            <a href="index.php" class="text-success d-block mb-2 text-decoration-none"><i class="bi bi-people"></i> Owners</a>
            <a href="../animals/index.php" class="text-white d-block mb-2 text-decoration-none"><i class="bi bi-cow"></i> Animals</a>
        </div>

        <!-- Main Content Section -->
        <div class="col-md-10 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4>Livestock Owners</h4>
                <!-- Kaliya Admin iyo Vet ayaa furi kara foomka cusub -->
                <?php if ($role == 'Admin' || $role == 'Veterinary Officer'): ?>
                    <a href="add.php" class="btn btn-success"><i class="bi bi-plus-lg"></i> Add New Owner</a>
                <?php endif; ?>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Region</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <!-- Qari tiirka Actions haddii uu yahay Export Officer -->
                                <?php if ($role == 'Admin' || $role == 'Veterinary Officer'): ?>
                                    <th class="text-end px-4">Actions</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($owners as $owner): ?>
                            <tr>
                                <!-- Hubi in magaca database-ka ku jira uu yahay 'name' ama 'fullname' (Waxaan u daayay 'name') -->
                                <td class="fw-bold text-dark px-3"><?= htmlspecialchars($owner['name'] ?? $owner['fullname']); ?></td>
                                <td><?= htmlspecialchars($owner['region']); ?></td>
                                <td><?= htmlspecialchars($owner['phone']); ?></td>
                                <td><?= htmlspecialchars($owner['email']); ?></td>
                                
                                <!-- Badhamada maamulka oo u muuqanaya kaliya Admin/Vet -->
                                <?php if ($role == 'Admin' || $role == 'Veterinary Officer'): ?>
                                    <td class="text-end px-4">
                                        <a href="edit.php?id=<?= $owner['id']; ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="delete.php?id=<?= $owner['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Ma hubtaa inaad tirtirto milkiilahan?')" title="Delete">
                                            <i class="bi bi-trash"></i>
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