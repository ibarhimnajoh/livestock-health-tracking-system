<?php
session_start();
if (!isset($_SESSION['user_id'])) { 
    header("Location: ../index.php"); 
    exit; 
}
require_once '../includes/db.php';

$role = $_SESSION['role'] ?? 'Export Officer';

try {
    // Advanced query fetching vaccination records securely with mapping
    $query = "SELECT v.id, v.vaccine_name, v.batch_number, v.vaccinated_at, v.expiry_date, a.animal_id AS rfid, a.species 
              FROM vaccinations v
              JOIN animals a ON v.animal_id = a.id
              ORDER BY v.id DESC";
    $vaccinations = $pdo->query($query)->fetchAll();

    // Computing Real-time Metrics
    $total_records = count($vaccinations);
    $expired_count = 0;
    $current_date = date('Y-m-d');
    foreach($vaccinations as $v) {
        if(!empty($v['expiry_date']) && $v['expiry_date'] < $current_date) {
            $expired_count++;
        }
    }
} catch (PDOException $e) {
    die("Database engine structural error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="so">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vaccination Ledger - VetExpert</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .hover-card { transition: all 0.2s ease-in-out; }
        .hover-card:hover { transform: translateY(-3px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important; }
        .search-focus:focus { box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.25); border-color: #198754; }
    </style>
</head>
<body class="bg-light">

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar Navigation Component -->
        <div class="col-md-2 bg-dark min-vh-100 p-3 text-white position-fixed shadow">
            <div class="d-flex align-items-center mb-3 mt-2">
                <i class="bi bi-shield-check text-success fs-3 me-2"></i>
                <h5 class="mb-0 text-uppercase fw-bold">VetExpert</h5>
            </div>
            <p class="small text-muted mb-4 border-bottom pb-2">Secure Node: <span class="badge bg-success"><?= htmlspecialchars($role); ?></span></p>
            
            <nav class="nav flex-column">
                <a href="../dashboard/dashboard.php" class="nav-link text-white mb-2 py-2 px-3 rounded"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
                <a href="../owners/index.php" class="nav-link text-white mb-2 py-2 px-3 rounded"><i class="bi bi-people me-2"></i> Owners Registry</a>
                <a href="../animals/index.php" class="nav-link text-white mb-2 py-2 px-3 rounded"><i class="bi bi-cow me-2"></i> Animals Ledger</a>
                <a href="index.php" class="nav-link text-white bg-success mb-2 py-2 px-3 rounded shadow-sm fw-bold"><i class="bi bi-patch-check me-2"></i> Vaccinations</a>
            </nav>
        </div>

        <!-- Main Content Viewport -->
        <div class="col-md-10 offset-md-2 p-4">
            
            <!-- Success Context Alert Notifications -->
            <?php if(isset($_GET['success']) && $_GET['success'] == 'VaccineRecorded'): ?>
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> Diiwaanka tallaalka cusub si guul leh ayaa loogu daray nidaamka guud!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- Action Matrix Header Header -->
            <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                <div>
                    <h2 class="mb-1 text-dark fw-bold">Vaccination Logs Registry</h2>
                    <p class="text-muted mb-0">Maareynta, hubinta, iyo dabagalka tallaalada xoolaha ee loo diyaarinayo dhoofka.</p>
                </div>
                <?php if ($role == 'Admin' || $role == 'Veterinary Officer'): ?>
                    <a href="add.php" class="btn btn-success px-4 py-2 shadow-sm fw-semibold"><i class="bi bi-plus-lg me-1"></i> Record New Vaccine</a>
                <?php endif; ?>
            </div>

            <!-- Statistical Analytics Widgets -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-3 p-3 hover-card">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="text-muted text-uppercase small mb-1 fw-bold">Total Doses Administered</h6>
                                <h3 class="mb-0 fw-bold text-dark"><?= $total_records; ?></h3>
                            </div>
                            <div class="bg-success bg-opacity-10 p-3 rounded-circle text-success"><i class="bi bi-capsule fs-4"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-3 p-3 hover-card">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="text-muted text-uppercase small mb-1 fw-bold">Expired Immunity Batches</h6>
                                <h3 class="mb-0 fw-bold text-danger"><?= $expired_count; ?></h3>
                            </div>
                            <div class="bg-danger bg-opacity-10 p-3 rounded-circle text-danger"><i class="bi bi-calendar-x fs-4"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-3 p-3 hover-card">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="text-muted text-uppercase small mb-1 fw-bold">Active Security Session</h6>
                                <h3 class="mb-0 fs-6 fw-bold text-truncate text-muted"><?= htmlspecialchars($_SESSION['fullname'] ?? 'Authorized Operator'); ?></h3>
                            </div>
                            <div class="bg-primary bg-opacity-10 p-3 rounded-circle text-primary"><i class="bi bi-shield-lock fs-4"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Real-time Interactive Lookup Filter -->
            <div class="card border-0 shadow-sm mb-4 rounded-3">
                <div class="card-body p-3">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" id="vaccineSearchInput" class="form-control border-start-0 search-focus" placeholder="Ku shaandhee RFID Tag, Nooca Tallaalka (Vaccine Name) ama Batch Number...">
                    </div>
                </div>
            </div>

            <!-- Data Table Engine Layout -->
            <div class="card shadow border-0 rounded-3 overflow-hidden">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="vaccineTable">
                            <thead class="table-dark text-uppercase small">
                                <tr>
                                    <th class="px-4 py-3">Animal ID (RFID)</th>
                                    <th class="py-3">Species</th>
                                    <th class="py-3">Vaccine Name</th>
                                    <th class="py-3">Batch / Serial Number</th>
                                    <th class="py-3">Vaccinated At</th>
                                    <th class="px-4 py-3">Expiry Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if($total_records > 0): ?>
                                    <?php foreach ($vaccinations as $vac): 
                                        $is_expired = (!empty($vac['expiry_date']) && $vac['expiry_date'] < $current_date);
                                        $badge_class = $is_expired ? 'bg-danger' : 'bg-warning text-dark';
                                    ?>
                                    <tr class="vaccine-row">
                                        <td class="px-4 py-3 fw-bold text-primary vaccine-rfid">#<?= htmlspecialchars($vac['rfid']); ?></td>
                                        <td class="text-dark fw-medium"><?= htmlspecialchars($vac['species']); ?></td>
                                        <td class="fw-bold text-dark vaccine-name"><i class="bi bi-patch-check-fill text-success me-1"></i><?= htmlspecialchars($vac['vaccine_name']); ?></td>
                                        <td><code class="text-secondary fw-semibold vaccine-batch"><?= htmlspecialchars($vac['batch_number']); ?></code></td>
                                        <td class="text-muted fw-medium"><?= htmlspecialchars($vac['vaccinated_at']); ?></td>
                                        <td class="px-4">
                                            <span class="badge <?= $badge_class; ?> px-3 py-2 rounded-pill shadow-sm">
                                                <?= htmlspecialchars($vac['expiry_date']); ?> 
                                                <?= $is_expired ? ' (Expired)' : ''; ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">
                                            <i class="bi bi-folder-x fs-1 d-block mb-2 text-secondary"></i> Weli wax diiwaan tallaal ah kuma jiraan nidaamka.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                                <!-- Empty state shown on lookup mismatches -->
                                <tr id="noVaccineMatch" style="display: none;">
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="bi bi-search-asterisk fs-1 d-block mb-2 text-warning"></i> Ma jiro diiwaan tallaal oo u dhigma baadhitaankaaga.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    // Live Dynamic Filtering Engine
    document.getElementById('vaccineSearchInput').addEventListener('keyup', function() {
        let filter = this.value.toLowerCase().trim();
        let rows = document.querySelectorAll('.vaccine-row');
        let matched = 0;

        rows.forEach(row => {
            let rfid = row.querySelector('.vaccine-rfid').textContent.toLowerCase();
            let name = row.querySelector('.vaccine-name').textContent.toLowerCase();
            let batch = row.querySelector('.vaccine-batch').textContent.toLowerCase();

            if(rfid.includes(filter) || name.includes(filter) || batch.includes(filter)) {
                row.style.display = '';
                matched++;
            } else {
                row.style.display = 'none';
            }
        });

        document.getElementById('noVaccineMatch').style.display = (matched === 0 && rows.length > 0) ? '' : 'none';
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 