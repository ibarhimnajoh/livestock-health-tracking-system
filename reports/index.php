<?php
session_start();
if (!isset($_SESSION['user_id'])) { 
    header("Location: ../index.php"); 
    exit; 
}
require_once '../includes/db.php';

// Xogta guud ee laga soo jiidayo Database-ka
$total_animals = $pdo->query("SELECT COUNT(*) FROM animals")->fetchColumn();
$total_owners = $pdo->query("SELECT COUNT(*) FROM owners")->fetchColumn();
$quarantined_animals = $pdo->query("SELECT COUNT(*) FROM animals WHERE status = 'Quarantined'")->fetchColumn();
$healthy_animals = $pdo->query("SELECT COUNT(*) FROM animals WHERE status = 'Healthy'")->fetchColumn();
$total_vaccinations = $pdo->query("SELECT COUNT(*) FROM vaccinations")->fetchColumn();

$recent_animals = $pdo->query("SELECT a.animal_id, a.species, a.status, o.name AS owner_name 
                               FROM animals a 
                               JOIN owners o ON a.owner_id = o.id 
                               ORDER BY a.id DESC LIMIT 8")->fetchAll();
?>
<!DOCTYPE html>
<html lang="so">
<head>
    <meta charset="UTF-8">
    <title>Official Statistical Report - VetExpert</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <style>
        /* Qaabaynta Shaashadda Caadiga ah */
        .print-header { display: none; }
        
        /* QAABAYNTA DAABACAADDA (PRINT STYLES) */
        @media print {
            body {
                background-color: #ffffff !important;
                color: #000000 !important;
                font-size: 12pt;
            }
            /* Qari Sidebar-ka iyo Badhamada nidaamka */
            .col-md-2, .btn, .no-print {
                display: none !important;
            }
            .col-md-10 {
                width: 100% !important;
                padding: 0 !important;
            }
            /* Muuji Madax-qoraalka Rasmiga ah (Letterhead) */
            .print-header {
                display: block !important;
                text-align: center;
                margin-bottom: 30px;
                border-bottom: 3px double #000000;
                padding-bottom: 10px;
            }
            /* Habayn toos ah oo loogu talagalay Kaadhadhka Xogta */
            .stats-box {
                border: 1px solid #dee2e6 !important;
                margin-bottom: 15px;
                background: #f8f9fa !important;
            }
            .card {
                border: 1px solid #dee2e6 !important;
                box-shadow: none !important;
            }
            .table th {
                background-color: #f8f9fa !important;
                color: #000000 !important;
            }
        }
    </style>
</head>
<body class="bg-light">

<div class="container-fluid">
    <div class="row">
        <div class="col-md-2 bg-dark min-vh-100 p-3 text-white no-print">
            <h5>VetExpert</h5>
            <hr>
            <a href="../dashboard/dashboard.php" class="text-white d-block mb-2 text-decoration-none"><i class="bi bi-speedometer2"></i> Dashboard</a>
            <a href="../owners/index.php" class="text-white d-block mb-2 text-decoration-none"><i class="bi bi-people"></i> Owners</a>
            <a href="../animals/index.php" class="text-white d-block mb-2 text-decoration-none"><i class="bi bi-cow"></i> Animals</a>
            <a href="../vaccinations/index.php" class="text-white d-block mb-2 text-decoration-none"><i class="bi bi-shield-check"></i> Vaccinations</a>
            <a href="index.php" class="text-success d-block mb-2 text-decoration-none"><i class="bi bi-bar-chart-line"></i> Reports</a>
        </div>

        <div class="col-md-10 p-4">
            
            <div class="print-header">
                <h2 class="fw-bold mb-1">VETEXPERT REGULATORY SYSTEM</h2>
                <h5 class="text-muted mb-2">Livestock Health & Export Tracking Department</h5>
                <p class="mb-0 text-secondary">Date generated: <?= date('d-M-Y H:i A'); ?> | Official Summary Report</p>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4 no-print">
                <h4><i class="bi bi-bar-chart-line-fill text-primary"></i> Analytical Reports</h4>
                <button onclick="window.print()" class="btn btn-primary fw-bold px-4">
                    <i class="bi bi-printer-fill"></i> Print Document
                </button>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-3">
                    <div class="card stats-box p-3 bg-white shadow-sm border-0">
                        <small class="text-muted fw-bold d-block mb-1">Total Animals</small>
                        <h3 class="fw-bold text-dark mb-0"><?= $total_animals; ?></h3>
                    </div>
                </div>
                <div class="col-3">
                    <div class="card stats-box p-3 bg-white shadow-sm border-0">
                        <small class="text-muted fw-bold d-block mb-1">Total Owners</small>
                        <h3 class="fw-bold text-dark mb-0"><?= $total_owners; ?></h3>
                    </div>
                </div>
                <div class="col-3">
                    <div class="card stats-box p-3 bg-white shadow-sm border-0">
                        <small class="text-muted fw-bold d-block mb-1">Healthy</small>
                        <h3 class="fw-bold text-success mb-0"><?= $healthy_animals; ?></h3>
                    </div>
                </div>
                <div class="col-3">
                    <div class="card stats-box p-3 bg-white shadow-sm border-0">
                        <small class="text-muted fw-bold d-block mb-1">In Quarantine</small>
                        <h3 class="fw-bold text-danger mb-0"><?= $quarantined_animals; ?></h3>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm p-4 bg-white">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold text-dark mb-0">Livestock Status Summary</h5>
                    <span class="text-muted small">Total Vaccinations Administered: <strong><?= $total_vaccinations; ?></strong></span>
                </div>
                
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-dark text-white">
                        <tr>
                            <th>RFID Tag</th>
                            <th>Species Type</th>
                            <th>Registered Owner</th>
                            <th>Health Condition</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_animals as $ra): ?>
                        <tr>
                            <td class="fw-bold text-monospace">#<?= htmlspecialchars($ra['animal_id']); ?></td>
                            <td><?= htmlspecialchars($ra['species']); ?></td>
                            <td><?= htmlspecialchars($ra['owner_name']); ?></td>
                            <td>
                                <span class="fw-semibold <?= $ra['status'] == 'Healthy' ? 'text-success' : 'text-danger'; ?>">
                                    <?= htmlspecialchars($ra['status']); ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <div class="row mt-5 pt-4 d-none d-print-flex justify-content-between">
                    <div class="col-4 text-center">
                        <hr class="border-dark">
                        <p class="fw-bold small mb-0">Prepared By (Veterinary Officer)</p>
                    </div>
                    <div class="col-4 text-center">
                        <hr class="border-dark">
                        <p class="fw-bold small mb-0">Authorized Stamp / Signature</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>