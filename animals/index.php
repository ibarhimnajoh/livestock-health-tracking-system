<?php
session_start();
if (!isset($_SESSION['user_id'])) { 
    header("Location: ../index.php"); 
    exit; 
}
require_once '../includes/db.php';

$role = $_SESSION['role'] ?? 'Export Officer';

try {
    // Advanced JOIN Query fetching records safely
    $query = "SELECT a.*, o.name as owner_name FROM animals a JOIN owners o ON a.owner_id = o.id ORDER BY a.id DESC";
    $animals = $pdo->query($query)->fetchAll();

    // Live Metrics Calculations
    $total_animals = count($animals);
    $quarantined = 0;
    foreach($animals as $an) {
        if($an['status'] == 'Quarantined') $quarantined++;
    }
} catch (PDOException $e) {
    die("Database structural query error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="so">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Animal Ledger Architecture - VetExpert</title>
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
        <!-- Sidebar Navigation -->
        <div class="col-md-2 bg-dark min-vh-100 p-3 text-white position-fixed shadow">
            <div class="d-flex align-items-center mb-3 mt-2">
                <i class="bi bi-shield-check text-success fs-3 me-2"></i>
                <h5 class="mb-0 text-uppercase fw-bold">VetExpert</h5>
            </div>
            <p class="small text-muted mb-4 border-bottom pb-2">Secure Node: <span class="badge bg-success"><?= htmlspecialchars($role); ?></span></p>
            
            <nav class="nav flex-column">
                <a href="../dashboard/dashboard.php" class="nav-link text-white mb-2 py-2 px-3 rounded"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
                <a href="../owners/index.php" class="nav-link text-white mb-2 py-2 px-3 rounded"><i class="bi bi-people me-2"></i> Owners Registry</a>
                <a href="index.php" class="nav-link text-white bg-success mb-2 py-2 px-3 rounded shadow-sm fw-bold"><i class="bi bi-cow me-2"></i> Animals Ledger</a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="col-md-10 offset-md-2 p-4">
            
            <!-- Context Feedback Alerts -->
            <?php if(isset($_GET['success']) && $_GET['success'] == 'AnimalUpdated'): ?>
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> Xogta astaan-aqoonsiga neefka iyo caafimaadkiisa waa la cusboonaysiiyey!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- Header Action Matrix -->
            <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                <div>
                    <h2 class="mb-1 text-dark fw-bold">Livestock Asset Ledger</h2>
                    <p class="text-muted mb-0">Dabagalka caafimaadka, noocyada xoolaha dhoofaya, iyo dabagalka RFID tags.</p>
                </div>
                <?php if ($role == 'Admin' || $role == 'Veterinary Officer'): ?>
                    <a href="register.php" class="btn btn-success px-4 py-2 shadow-sm fw-semibold"><i class="bi bi-plus-lg me-1"></i> Register New Animal</a>
                <?php endif; ?>
            </div>

            <!-- Statistical Analytics Widgets -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-3 p-3 hover-card">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="text-muted text-uppercase small mb-1 fw-bold">Total Animal Assets</h6>
                                <h3 class="mb-0 fw-bold text-dark"><?= $total_animals; ?></h3>
                            </div>
                            <div class="bg-success bg-opacity-10 p-3 rounded-circle text-success"><i class="bi bi-cow fs-4"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-3 p-3 hover-card">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="text-muted text-uppercase small mb-1 fw-bold">Currently Quarantined</h6>
                                <h3 class="mb-0 fw-bold text-warning"><?= $quarantined; ?></h3>
                            </div>
                            <div class="bg-warning bg-opacity-10 p-3 rounded-circle text-warning"><i class="bi bi-shield-exclamation fs-4"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-3 p-3 hover-card">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="text-muted text-uppercase small mb-1 fw-bold">Operator Session</h6>
                                <h3 class="mb-0 fs-6 fw-bold text-truncate text-muted"><?= htmlspecialchars($_SESSION['fullname'] ?? 'Secured System'); ?></h3>
                            </div>
                            <div class="bg-primary bg-opacity-10 p-3 rounded-circle text-primary"><i class="bi bi-person-workspace fs-4"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Real-time Filter Component -->
            <div class="card border-0 shadow-sm mb-4 rounded-3">
                <div class="card-body p-3">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" id="animalSearchInput" class="form-control border-start-0 search-focus" placeholder="Ku shaandhee RFID, Nooca neefka (Species), ama Milkiilaha si degdeg ah...">
                    </div>
                </div>
            </div>

            <!-- Data Table Engine -->
            <div class="card shadow border-0 rounded-3 overflow-hidden">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="animalTable">
                            <thead class="table-dark text-uppercase small">
                                <tr>
                                    <th class="px-4 py-3">Animal ID (RFID)</th>
                                    <th class="py-3">Species / Breed</th>
                                    <th class="py-3">Owner / Farm Context</th>
                                    <th class="py-3">Status</th>
                                    <?php if ($role == 'Admin' || $role == 'Veterinary Officer'): ?>
                                        <th class="text-end px-4 py-3">Operations</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if($total_animals > 0): ?>
                                    <?php foreach ($animals as $animal): 
                                        $badge = 'bg-success';
                                        if($animal['status'] == 'Quarantined') $badge = 'bg-warning text-dark';
                                        if($animal['status'] == 'Treatment') $badge = 'bg-danger';
                                        if($animal['status'] == 'Exported') $badge = 'bg-info text-dark';
                                    ?>
                                    <tr class="animal-row">
                                        <td class="px-4 py-3 fw-bold text-primary animal-rfid">#<?= htmlspecialchars($animal['animal_id']); ?></td>
                                        <td class="animal-spec">
                                            <span class="fw-semibold text-dark"><?= htmlspecialchars($animal['species']); ?></span>
                                            <small class="text-muted d-block fs-7"><?= !empty($animal['breed']) ? htmlspecialchars($animal['breed']) : 'Local Breed'; ?></small>
                                        </td>
                                        <td class="text-dark fw-medium animal-owner"><i class="bi bi-person me-1 text-muted"></i><?= htmlspecialchars($animal['owner_name']); ?></td>
                                        <td><span class="badge <?= $badge; ?> px-3 py-2 rounded-pill"><?= $animal['status']; ?></span></td>
                                        
                                        <?php if ($role == 'Admin' || $role == 'Veterinary Officer'): ?>
                                            <td class="text-end px-4">
                                                <div class="btn-group shadow-sm border rounded bg-white">
                                                    <a href="../health/add_record.php?animal_id=<?= $animal['id']; ?>" class="btn btn-sm btn-light py-2 px-3 text-secondary border-end" title="Check Health Ecosystem">
                                                        <i class="bi bi-heart-pulse-fill text-danger"></i>
                                                    </a>
                                                    <a href="edit.php?id=<?= $animal['id']; ?>" class="btn btn-sm btn-light py-2 px-3 text-primary" title="Modify Asset Matrix">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        <?php endif; ?>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <i class="bi bi-folder-x fs-1 d-block mb-2 text-secondary"></i> Weli wax xoolo ah kama diwaangashna nidaamka.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                                <!-- Empty state shown on lookup mismatches -->
                                <tr id="noAnimalMatch" style="display: none;">
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="bi bi-search-asterisk fs-1 d-block mb-2 text-warning"></i> Ma jiro neef u dhigma baadhitaankaaga.
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
    // Live Dynamic Search Filter Engine
    document.getElementById('animalSearchInput').addEventListener('keyup', function() {
        let filter = this.value.toLowerCase().trim();
        let rows = document.querySelectorAll('.animal-row');
        let matched = 0;

        rows.forEach(row => {
            let rfid = row.querySelector('.animal-rfid').textContent.toLowerCase();
            let spec = row.querySelector('.animal-spec').textContent.toLowerCase();
            let owner = row.querySelector('.animal-owner').textContent.toLowerCase();

            if(rfid.includes(filter) || spec.includes(filter) || owner.includes(filter)) {
                row.style.display = '';
                matched++;
            } else {
                row.style.display = 'none';
            }
        });

        document.getElementById('noAnimalMatch').style.display = (matched === 0 && rows.length > 0) ? '' : 'none';
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>