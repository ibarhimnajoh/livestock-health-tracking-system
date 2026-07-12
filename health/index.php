<?php
session_start();
if (!isset($_SESSION['user_id'])) { 
    header("Location: ../index.php"); 
    exit; 
}
require_once '../includes/db.php';

try {
    $query = "SELECT 
                h.id, h.diagnosis, h.status, h.inspection_date, 
                a.animal_id as rfid, a.species, u.fullname as vet_name 
              FROM health_records h 
              INNER JOIN animals a ON h.animal_id = a.id 
              INNER JOIN users u ON h.vet_id = u.id 
              ORDER BY h.inspection_date DESC, h.id DESC 
              LIMIT 100";
    $inspections = $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="so">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VetExpert - Health Analytics</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f1f5f9; color: #1e293b; }
        .sidebar { background: #1e293b; min-vh-100; box-shadow: 2px 0 5px rgba(0,0,0,0.05); }
        .nav-link-custom { color: #94a3b8; padding: 0.75rem 1rem; border-radius: 0.5rem; display: flex; align-items: center; gap: 10px; transition: all 0.2s; text-decoration: none; }
        .nav-link-custom:hover { background: #334155; color: #f8fafc; }
        .nav-link-custom.active { background: #10b981; color: white; }
        .card-premium { border-radius: 1rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -1px rgba(0,0,0,0.03); border: none; }
        .table th { font-weight: 600; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em; color: #64748b; background-color: #f8fafc; padding: 1rem; border-bottom: 2px solid #e2e8f0; }
        .table td { padding: 1.2rem 1rem; vertical-align: middle; font-size: 0.875rem; border-bottom: 1px solid #e2e8f0; }
        .badge-status { font-weight: 500; padding: 0.4rem 0.8rem; border-radius: 30px; font-size: 0.75rem; display: inline-flex; align-items: center; gap: 5px; }
    </style>
</head>
<body>

<div class="container-fluid p-0">
    <div class="row g-0">
        <!-- Sidebar Navigation -->
        <div class="col-md-2 sidebar p-4 d-flex flex-column justify-content-between">
            <div>
                <div class="d-flex align-items-center gap-2 px-2 mb-4 text-white">
                    <i class="bi bi-shield-minus text-success fs-3"></i>
                    <span class="fw-bold fs-5">VetExpert</span>
                </div>
                <div class="nav flex-column gap-1">
                    <a href="../dashboard/dashboard.php" class="nav-link-custom"><i class="bi bi-grid-1x2"></i> Dashboard</a>
                    <a href="index.php" class="nav-link-custom active"><i class="bi bi-heart-pulse"></i> Health Index</a>
                    <a href="../vaccinations/index.php" class="nav-link-custom"><i class="bi bi-shield-plus"></i> Vaccinations</a>
                    <a href="../animals/index.php" class="nav-link-custom"><i class="bi bi-tags"></i> Animal Registry</a>
                </div>
            </div>
            <div class="text-muted border-top pt-3 border-secondary" style="font-size: 11px;">
                Vet Officer: <strong class="text-light"><?= htmlspecialchars($_SESSION['username'] ?? 'Vet'); ?></strong>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="col-md-10 p-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold text-slate-800 m-0">Health Inspections</h3>
                    <p class="text-muted small m-0">Taariikhda baadhitaanada caafimaadka ee xoolaha ku jira maxjarka.</p>
                </div>
                <a href="../animals/index.php" class="btn text-white px-4 py-2" style="background-color: #10b981; border-radius: 0.5rem; font-weight: 500;">
                    <i class="bi bi-plus-lg me-2"></i> Diwaangeli Baadhitaan
                </a>
            </div>

            <!-- Table Card -->
            <div class="card card-premium bg-white">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Animal ID (RFID)</th>
                                    <th>Species / Nooca</th>
                                    <th>Diagnosis Findings / Baadhistii</th>
                                    <th>Health Status</th>
                                    <th>Inspection Date</th>
                                    <th>Veterinary Officer</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($inspections)): ?>
                                    <?php foreach ($inspections as $ins): ?>
                                    <tr>
                                        <td class="fw-semibold text-dark font-monospace">#<?= htmlspecialchars($ins['rfid']); ?></td>
                                        <td><span class="badge bg-light text-dark border px-2 py-1.5"><?= htmlspecialchars($ins['species'] ?? 'Kamid ah'); ?></span></td>
                                        <td class="text-secondary text-wrap" style="max-width: 320px;"><?= htmlspecialchars($ins['diagnosis']); ?></td>
                                        <td>
                                            <?php 
                                            $status = $ins['status'];
                                            $badge = 'bg-success-subtle text-success border border-success-subtle';
                                            if ($status === 'Quarantined') $badge = 'bg-danger-subtle text-danger border border-danger-subtle';
                                            if ($status === 'Treatment') $badge = 'bg-warning-subtle text-warning-emphasis border border-warning-subtle';
                                            ?>
                                            <span class="badge badge-status <?= $badge; ?>">
                                                <i class="bi bi-circle-fill" style="font-size: 6px;"></i> <?= htmlspecialchars($status); ?>
                                            </span>
                                        </td>
                                        <td class="text-muted font-monospace"><?= date('M d, Y', strtotime($ins['inspection_date'])); ?></td>
                                        <td class="fw-medium text-dark"><i class="bi bi-person-check text-muted me-1"></i> <?= htmlspecialchars($ins['vet_name']); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-5">
                                            <i class="bi bi-folder-x fs-2 d-block mb-2 text-secondary"></i>
                                            Wali wax diwaangalin caafimaad ah laguma samayn nidaamka.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>