<?php
session_start();

// 1. Amniga: Hubi in qofku soo galay nidaamka (Authenticated)
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/index.php");
    exit;
}

// 2. Ku xidhista Database-ka
require_once '../includes/db.php';

$role = $_SESSION['role'];

// 3. Tirokoobka Metric Cards-ka (Dynamic Counters)
$total_animals = $pdo->query("SELECT COUNT(*) FROM animals")->fetchColumn();
$pending_permits = $pdo->query("SELECT COUNT(*) FROM export_permits WHERE status='Pending'")->fetchColumn();
$active_alerts = $pdo->query("SELECT COUNT(*) FROM animals WHERE status='Quarantined' OR status='Treatment'")->fetchColumn();

// 4. Tirokoobka Gobollada ee Khariidadda (GIS Map Feed)
$maroodi_jeex_count = $pdo->query("SELECT COUNT(*) FROM animals a JOIN owners o ON a.owner_id = o.id WHERE o.region = 'Maroodi Jeex'")->fetchColumn();
$togdheer_count = $pdo->query("SELECT COUNT(*) FROM animals a JOIN owners o ON a.owner_id = o.id WHERE o.region = 'Togdheer'")->fetchColumn();
$saaxil_count = $pdo->query("SELECT COUNT(*) FROM animals a JOIN owners o ON a.owner_id = o.id WHERE o.region = 'Saaxil'")->fetchColumn();

// 5. Baadhista Xogta Miiska Hoose (Recent Activity) iyadoo loo eegayo Doorka (Role-Based)
if ($role == 'Veterinary Officer') {
    // Dhakhtarku wuxuu arkaa baaritaanadii caafimaadka ee ugu dambeeyey
    $query = "SELECT a.animal_id AS rfid, a.species, h.diagnosis, u.fullname AS inspector, a.status, h.inspection_date 
              FROM health_records h
              JOIN animals a ON h.animal_id = a.id
              JOIN users u ON h.vet_id = u.id
              ORDER BY h.id DESC LIMIT 5";
} else if ($role == 'Export Officer') {
    // Sarkaalka dhoofku wuxuu arkaa ruqsadaha dhoofka
    $query = "SELECT a.animal_id AS rfid, a.species, e.destination_country AS diagnosis, u.fullname AS inspector, e.status, e.created_at AS inspection_date 
              FROM export_permits e
              JOIN animals a ON e.animal_id = a.id
              JOIN users u ON e.officer_id = u.id
              ORDER BY e.id DESC LIMIT 5";
} else {
    // Admin-ku wuxuu arkaa wax kasta (Default View)
    $query = "SELECT a.animal_id AS rfid, a.species, h.diagnosis, u.fullname AS inspector, a.status, h.inspection_date 
              FROM health_records h
              JOIN animals a ON h.animal_id = a.id
              JOIN users u ON h.vet_id = u.id
              ORDER BY h.id DESC LIMIT 5";
}
$recent_activities = $pdo->query($query)->fetchAll();
?>
<!DOCTYPE html>
<html lang="so">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VetExpert - Dashboard Overview</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    
    <style>
        /* Pulse Animation-ka Khariidadda Gobollada */
        @keyframes ping {
            75%, 100% { transform: scale(2.5); opacity: 0; }
        }
        .animate-ping {
            animation: ping 1.5s cubic-bezier(0, 0, 0.2, 1) infinite;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-brand">
        <h4>VetExpert</h4>
        <small>Regulatory System</small>
    </div>
    <div class="mt-4">
        <a href="dashboard.php" class="active"><i class="bi bi-grid-fill"></i> Dashboard</a>
        <a href="../owners/index.php"><i class="bi bi-person-lines-fill"></i> Owners</a>
        <a href="../animals/index.php"><i class="bi bi-cow"></i> Animals</a>        
        <?php if ($role == 'Admin' || $role == 'Veterinary Officer'): ?>
            <a href="../vaccinations/index.php"><i class="bi bi-shield-check"></i> Vaccination Records</a>
            <a href="../health/index.php"><i class="bi bi-heart-pulse"></i> Health Inspections</a>
        <?php endif; ?>
        
        <?php if ($role == 'Admin' || $role == 'Export Officer'): ?>
            <a href="../exports/index.php"><i class="bi bi-file-earmark-text"></i> Export Permits</a>
        <?php endif; ?>
        
        <?php if ($role == 'Admin'): ?>
            <a href="../reports/index.php"><i class="bi bi-bar-chart-line"></i> Reports</a>
            <!-- SETTINGS CUSUB EE LAGU DARAY SIDEBAR-KA -->
            <a href="../settings/index.php"><i class="bi bi-gear-fill"></i> Settings</a>
        <?php endif; ?>
    </div>
    <div class="sidebar-footer">
        <a href="../auth/index.php" class="text-danger"><i class="bi bi-box-arrow-left text-danger"></i> Sign Out</a>
    </div>
</div>

<div class="main-content">
    
    <div class="topbar">
        <div class="search-container">
            <i class="bi bi-search"></i>
            <input type="text" class="search-box" placeholder="Search animals, permits, or status...">
        </div>
        <div class="d-flex align-items-center gap-4">
            <h5 class="m-0" style="color: #1b4324; font-weight:700;">Livestock Export Authority</h5>
            <div class="d-flex align-items-center gap-2">
                <div class="text-end">
                    <div class="fw-bold" style="font-size:13px;"><?= htmlspecialchars($_SESSION['fullname'] ?? 'User'); ?></div>
                    <span class="badge bg-success" style="font-size:10px;"><?= $role; ?></span>
                </div>
                <i class="bi bi-person-circle fs-3 text-secondary"></i>
            </div>
        </div>
    </div>

    <div class="container-fluid p-4">
        
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="metric-card">
                    <div class="metric-title">Total Registered Animals</div>
                    <div class="metric-value"><?= number_format($total_animals); ?></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="metric-card">
                    <div class="metric-title">Pending Export Permits</div>
                    <div class="metric-value"><?= $pending_permits; ?></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="metric-card alert-card">
                    <div class="metric-title">Active Health Alerts</div>
                    <div class="metric-value"><?= sprintf("%02d", $active_alerts); ?></div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-8">
                <div class="card border-0 shadow-sm p-3 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold text-dark" style="font-size:14px;">Regional Livestock Distribution</span>
                        <span class="badge bg-success bg-opacity-10 text-success px-2 py-1" style="font-size:11px;">
                            <span class="spinner-grow spinner-grow-sm text-success" style="width:7px; height:7px;"></span> Live GIS Feed
                        </span>
                    </div>
                    
                    <div class="map-placeholder position-relative overflow-hidden" style="background: #f8fafc; border: 1px solid #e2e8f0; height: 340px; border-radius: 4px;">
                        <div style="position: absolute; inset: 0; opacity: 0.05; background-image: linear-gradient(#1b4324 2px, transparent 2px), linear-gradient(90deg, #1b4324 2px, transparent 2px); background-size: 20px 20px;"></div>

                        <div style="position: absolute; top: 35%; left: 25%;" class="text-center">
                            <span class="animate-ping" style="position: absolute; display: inline-flex; width: 20px; height: 20px; background: rgba(42,90,50,0.4); border-radius: 50%; transform: translate(-20%, -20%);"></span>
                            <div style="width: 10px; height: 10px; background: #1b4324; border-radius: 50%; margin: 0 auto; position:relative;"></div>
                            <div class="bg-white px-2 py-1 shadow-sm rounded border text-nowrap mt-1" style="font-size: 10px; font-weight: 700;">
                                Maroodi Jeex: <span class="text-success"><?= $maroodi_jeex_count; ?> Head</span>
                            </div>
                        </div>

                        <div style="position: absolute; top: 20%; left: 50%;" class="text-center">
                            <span class="animate-ping" style="position: absolute; display: inline-flex; width: 20px; height: 20px; background: rgba(42,90,50,0.4); border-radius: 50%; transform: translate(-20%, -20%);"></span>
                            <div style="width: 10px; height: 10px; background: #1b4324; border-radius: 50%; margin: 0 auto; position:relative;"></div>
                            <div class="bg-white px-2 py-1 shadow-sm rounded border text-nowrap mt-1" style="font-size: 10px; font-weight: 700;">
                                Saaxil: <span class="text-success"><?= $saaxil_count; ?> Head</span>
                            </div>
                        </div>

                        <div style="position: absolute; top: 60%; left: 70%;" class="text-center">
                            <span class="animate-ping" style="position: absolute; display: inline-flex; width: 20px; height: 20px; background: rgba(220,38,38,0.3); border-radius: 50%; transform: translate(-20%, -20%);"></span>
                            <div style="width: 10px; height: 10px; background: #dc2626; border-radius: 50%; margin: 0 auto; position:relative;"></div>
                            <div class="bg-white px-2 py-1 shadow-sm rounded border text-nowrap mt-1" style="font-size: 10px; font-weight: 700;">
                                Togdheer: <span class="text-danger"><?= $togdheer_count; ?> Head</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="row g-2 h-100">
                    <?php if ($role == 'Admin' || $role == 'Veterinary Officer'): ?>
                        <div class="col-6">
                            <a href="../animals/register.php" class="text-decoration-none action-btn-green">
                                <i class="bi bi-plus-circle fs-4 mb-2"></i>
                                <span>New Animal</span>
                            </a>
                        </div>
                        <div class="col-6">
                            <div class="action-btn-white" style="cursor: pointer;">
                                <i class="bi bi-heart-pulse fs-4 mb-2 text-danger"></i>
                                <span>Health Check</span>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($role == 'Admin' || $role == 'Export Officer'): ?>
                        <div class="col-6">
                            <a href="../exports/create.php" class="text-decoration-none action-btn-white">
                                <i class="bi bi-file-earmark-text fs-4 mb-2 text-primary"></i>
                                <span>Issue Permit</span>
                            </a>
                        </div>
                    <?php endif; ?>
                    
                    <div class="col-6">
                        <div class="action-btn-white" id="scan-trigger" style="cursor: pointer;">
                            <i class="bi bi-qr-code-scan fs-4 mb-2 text-success"></i>
                            <span>Scan RFID Tag</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm p-3">
            <span class="fw-bold mb-3" style="font-size:14px;">Recent Activity Logs (<?= $role; ?> Dashboard View)</span>
            <div class="table-responsive">
                <table id="activity-table" class="table table-hover align-middle mb-0" style="font-size:13px;">
                    <thead class="table-light">
                        <tr style="font-size:11px; text-transform: uppercase; color: #6b7280;">
                            <th>ID / Batch</th>
                            <th>Species</th>
                            <th><?= $role == 'Export Officer' ? 'Destination Country' : 'Diagnosis/Findings'; ?></th>
                            <th>Handler / Officer</th>
                            <th>Status</th>
                            <th>Date Logged</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($recent_activities) > 0): ?>
                            <?php foreach ($recent_activities as $activity): ?>
                                <tr>
                                    <td class="fw-bold text-secondary"><?= htmlspecialchars($activity['rfid']); ?></td>
                                    <td class="fw-bold text-dark"><?= htmlspecialchars($activity['species']); ?></td>
                                    <td><?= htmlspecialchars($activity['diagnosis']); ?></td>
                                    <td><i class="bi bi-person-badge text-success me-1"></i> <?= htmlspecialchars($activity['inspector']); ?></td>
                                    <td>
                                        <?php 
                                        $st = strtolower($activity['status']);
                                        if ($st == 'healthy' || $st == 'approved') {
                                            echo '<span class="badge bg-success bg-opacity-10 text-success px-2 py-1">'.strtoupper($activity['status']).'</span>';
                                        } else if ($st == 'quarantined' || $st == 'rejected') {
                                            echo '<span class="badge bg-danger bg-opacity-10 text-danger px-2 py-1">'.strtoupper($activity['status']).'</span>';
                                        } else {
                                            echo '<span class="badge bg-warning bg-opacity-10 text-warning px-2 py-1">'.strtoupper($activity['status']).'</span>';
                                        }
                                        ?>
                                    </td>
                                    <td class="text-muted"><?= $activity['inspection_date']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="text-center text-muted py-3">Ma jirto wax xog ah oo dhowaan la diiwangeliyey.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <div class="bottom-status-bar">
        <div>● System Security Profile: Active Node &nbsp;|&nbsp; Location: Hargeisa</div>
        <div>Authorized Mode: <strong><?= strtoupper($role); ?></strong></div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const searchInput = document.querySelector(".search-box");
    const tableRows = document.querySelectorAll("#activity-table tbody tr");

    if (searchInput) {
        searchInput.addEventListener("keyup", function(e) {
            const searchFilter = e.target.value.toLowerCase().trim();

            tableRows.forEach(row => {
                const rowText = row.textContent.toLowerCase();
                if (rowText.includes(searchFilter)) {
                    row.style.display = ""; 
                } else {
                    row.style.display = "none"; 
                }
            });
        });
    }
    
    // Scan RFID Alerts Mock trigger
    const scanTrigger = document.getElementById("scan-trigger");
    if(scanTrigger) {
        scanTrigger.addEventListener("click", function() {
            alert("RFID Frequency Scanner Active... Searching for local tag signal.");
        });
    }
});
</script>

</body>
</html>