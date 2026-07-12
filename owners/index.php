<?php
session_start();
if (!isset($_SESSION['user_id'])) { 
    header("Location: ../index.php"); 
    exit; 
}
require_once '../includes/db.php';

// Role management
$role = $_SESSION['role'] ?? 'Export Officer';

// Fetch owners cleanly with dynamic counting for stats
try {
    $stmt = $pdo->query("SELECT * FROM owners ORDER BY id DESC");
    $owners = $stmt->fetchAll();

    // Stats calculations
    $total_owners = count($owners);
    
    $stmt_regions = $pdo->query("SELECT COUNT(DISTINCT region) AS total_regions FROM owners");
    $total_regions = $stmt_regions->fetch()['total_regions'] ?? 0;
} catch (PDOException $e) {
    die("Database structural query error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="so">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Livestock Owners Ledger - VetExpert</title>
    <!-- Bootstrap 5 & Google Fonts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .hover-card { transition: all 0.2s ease-in-out; }
        .hover-card:hover { transform: translateY(-3px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important; }
        .sidebar-link:hover { bg-color: rgba(255,255,255,0.1); }
        .search-focus:focus { box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.25); border-color: #198754; }
    </style>
</head>
<body class="bg-light">

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar Menu -->
        <div class="col-md-2 bg-dark min-vh-100 p-3 text-white position-fixed shadow">
            <div class="d-flex align-items-center mb-3 mt-2">
                <i class="bi bi-shield-check text-success fs-3 me-2"></i>
                <h5 class="mb-0 text-uppercase tracking-wider fw-bold">VetExpert</h5>
            </div>
            <p class="small text-muted mb-4 border-bottom pb-2">Session Secure: <span class="badge bg-success"><?= htmlspecialchars($role); ?></span></p>
            
            <nav class="nav flex-column">
                <a href="../dashboard/dashboard.php" class="nav-link text-white mb-2 py-2 px-3 rounded sidebar-link"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
                <a href="index.php" class="nav-link text-white bg-success mb-2 py-2 px-3 rounded shadow-sm fw-bold"><i class="bi bi-people me-2"></i> Owners Registry</a>
                <a href="../animals/index.php" class="nav-link text-white mb-2 py-2 px-3 rounded sidebar-link"><i class="bi bi-cow me-2"></i> Animals Ledger</a>
            </nav>
        </div>

        <!-- Main Content Section -->
        <div class="col-md-10 offset-md-2 p-4">
            
            <!-- Context Alerts -->
            <?php if(isset($_GET['success']) && $_GET['success'] == 'added'): ?>
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> Milkiilaha cusub iyo xogtiisa farm-ka waa la diwaangeliyey si guul ah!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- Action Heading -->
            <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                <div>
                    <h2 class="mb-1 text-dark fw-bold">Livestock Owners Ledger</h2>
                    <p class="text-muted mb-0">Maamulka, dabagalka, iyo xaqiijinta aqoonsiga milkiilayaasha xoolaha.</p>
                </div>
                <div class="d-flex gap-2">
                    <button onclick="exportTableToCSV('owners-export.csv')" class="btn btn-outline-secondary d-flex align-items-center shadow-sm"><i class="bi bi-download me-2"></i> Export CSV</button>
                    <?php if ($role == 'Admin' || $role == 'Veterinary Officer'): ?>
                        <a href="add.php" class="btn btn-success px-4 py-2 shadow-sm fw-semibold"><i class="bi bi-plus-lg me-1"></i> Add New Owner</a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Analytical Widgets Mini-Dashboard -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-3 p-3 hover-card">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="text-muted text-uppercase small mb-1 fw-bold">Total Active Owners</h6>
                                <h3 class="mb-0 fw-bold text-dark"><?= $total_owners; ?></h3>
                            </div>
                            <div class="bg-success bg-opacity-10 p-3 rounded-circle text-success"><i class="bi bi-people-fill fs-4"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-3 p-3 hover-card">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="text-muted text-uppercase small mb-1 fw-bold">Regions Covered</h6>
                                <h3 class="mb-0 fw-bold text-dark"><?= $total_regions; ?> <span class="text-muted fs-6 fw-normal">Gobol</span></h3>
                            </div>
                            <div class="bg-primary bg-opacity-10 p-3 rounded-circle text-primary"><i class="bi bi-geo-fill fs-4"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-3 p-3 hover-card">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="text-muted text-uppercase small mb-1 fw-bold">System Load Context</h6>
                                <h3 class="mb-0 fs-5 fw-bold text-dark text-truncate"><?= htmlspecialchars($_SESSION['fullname'] ?? 'User Engine'); ?></h3>
                            </div>
                            <div class="bg-warning bg-opacity-10 p-3 rounded-circle text-warning"><i class="bi bi-person-badge-fill fs-4"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Live Search Filter Component -->
            <div class="card border-0 shadow-sm mb-4 rounded-3">
                <div class="card-body p-3">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" id="ownerSearchInput" class="form-control border-start-0 search-focus" placeholder="Ku shaandhee magaca, gobolka, ama nambarka taleefanka si dhakhso ah...">
                    </div>
                </div>
            </div>

            <!-- Datatable Base Ledger -->
            <div class="card shadow border-0 rounded-3 overflow-hidden">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="ownersTable">
                            <thead class="table-dark text-uppercase small">
                                <tr>
                                    <th class="px-4 py-3">Owner Details</th>
                                    <th class="py-3">Geographic Region</th>
                                    <th class="py-3">Phone Terminal</th>
                                    <th class="py-3">Digital Mail Address</th>
                                    <?php if ($role == 'Admin' || $role == 'Veterinary Officer'): ?>
                                        <th class="text-end px-4 py-3">Operations Management</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if($total_owners > 0): ?>
                                    <?php foreach ($owners as $owner): ?>
                                    <tr class="owner-row">
                                        <td class="px-4 py-3">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-success bg-opacity-10 p-2 rounded-circle me-3 border border-success border-opacity-25">
                                                    <i class="bi bi-person-fill text-success fs-5"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark owner-name"><?= htmlspecialchars($owner['name'] ?? $owner['fullname']); ?></div>
                                                    <small class="text-muted fs-7 d-block"><i class="bi bi-pin-map me-1"></i><?= !empty($owner['address']) ? htmlspecialchars($owner['address']) : 'No farm address logged'; ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light border text-dark px-3 py-2 rounded-pill owner-region">
                                                <i class="bi bi-geo-alt-fill text-danger me-1"></i><?= htmlspecialchars($owner['region']); ?>
                                            </span>
                                        </td>
                                        <td class="text-dark fw-semibold owner-phone"><i class="bi bi-telephone-outbound text-muted small me-1"></i> <?= htmlspecialchars($owner['phone']); ?></td>
                                        <td class="text-muted small"><?= !empty($owner['email']) ? htmlspecialchars($owner['email']) : '<em>— N/A —</em>'; ?></td>
                                        
                                        <?php if ($role == 'Admin' || $role == 'Veterinary Officer'): ?>
                                            <td class="text-end px-4">
                                                <div class="btn-group shadow-sm border rounded bg-white">
                                                    <a href="edit.php?id=<?= $owner['id']; ?>" class="btn btn-sm btn-light py-2 px-3 text-primary border-end" title="Edit Profile">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </a>
                                                    <a href="delete.php?id=<?= $owner['id']; ?>" class="btn btn-sm btn-light py-2 px-3 text-danger" onclick="return confirm('Ma hubtaa inaad tirtirto milkiilahan? Dhamaan xoolaha ku xidhan wey tirtirmo karaan.')" title="Delete Profile">
                                                        <i class="bi bi-trash3-fill"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        <?php endif; ?>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr id="noDataRow">
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <i class="bi bi-folder-x fs-1 d-block mb-2 text-secondary"></i> Weli wax milkiilayaal ahi kama jiraan nidaamka.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                                <!-- Fallback dynamically shown via JS when filter yields zero records -->
                                <tr id="noMatchRow" style="display: none;">
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="bi bi-search-asterisk fs-1 d-block mb-2 text-warning"></i> Wax xog ah oo u dhigma baadhitaankaaga lama helin.
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

<!-- Embedded Pro Interactions Scripting -->
<script>
    // Real-time Client-side Search Engine
    document.getElementById('ownerSearchInput').addEventListener('keyup', function() {
        let filter = this.value.toLowerCase().trim();
        let rows = document.querySelectorAll('.owner-row');
        let matchedCount = 0;

        rows.forEach(row => {
            let name = row.querySelector('.owner-name').textContent.toLowerCase();
            let region = row.querySelector('.owner-region').textContent.toLowerCase();
            let phone = row.querySelector('.owner-phone').textContent.toLowerCase();

            if (name.includes(filter) || region.includes(filter) || phone.includes(filter)) {
                row.style.display = '';
                matchedCount++;
            } else {
                row.style.display = 'none';
            }
        });

        // Toggle No Match Display Row
        let noMatchRow = document.getElementById('noMatchRow');
        if (noMatchRow) {
            noMatchRow.style.display = (matchedCount === 0 && rows.length > 0) ? '' : 'none';
        }
    });

    // Enterprise CSV Export Protocol
    function exportTableToCSV(filename) {
        let csv = [];
        let rows = document.querySelectorAll("#ownersTable tr");
        
        for (let i = 0; i < rows.length; i++) {
            // Skip operations row/cells during structural conversion
            if(rows[i].id === 'noMatchRow' || rows[i].style.display === 'none') continue;
            
            let row = [], cols = rows[i].querySelectorAll("td, th");
            
            for (let j = 0; j < cols.length; j++) {
                // Do not export actions column
                if(j === 4) continue; 
                let data = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, "").replace(/(\s\s+)/gm, ' ');
                data = data.replace(/"/g, '""');
                row.push('"' + data + '"');
            }
            csv.push(row.join(","));
        }
        let csvFile = new Blob([csv.join("\n")], {type: "text/csv;charset=utf-8;"});
        let downloadLink = document.createElement("a");
        downloadLink.download = filename;
        downloadLink.href = window.URL.createObjectURL(csvFile);
        downloadLink.style.display = "none";
        document.body.appendChild(downloadLink);
        downloadLink.click();
        document.body.removeChild(downloadLink);
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>