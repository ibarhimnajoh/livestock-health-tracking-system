<?php
session_start();

// 1. Amniga: Hubi in qofku soo galay nidaamka
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/index.php");
    exit;
}

// 2. KALIYA ADMIN ayaa arki kara badalina kara settings-ka guud iyo dadka cusub
if ($_SESSION['role'] !== 'Admin') {
    header("Location: ../dashboard/dashboard.php");
    exit;
}

// 3. Ku xidhista Database-ka
require_once '../includes/db.php';

$role = $_SESSION['role'];
$user_id = $_SESSION['user_id'];
$success_msg = "";
$error_msg = "";

// 4. Soo jiidashada xogta Admin-ka hadda galay
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$current_user = $stmt->fetch();

// 5. Marka foomamka la soo gudbiyo (POST Actions)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // ACTION A: Admin-ka oo profile-kiisa beddelaya
    if (isset($_POST['update_profile'])) {
        $fullname = trim($_POST['fullname']);
        $email = trim($_POST['email']);
        
        if (!empty($fullname) && !empty($email)) {
            $update = $pdo->prepare("UPDATE users SET fullname = ?, email = ? WHERE id = ?");
            if ($update->execute([$fullname, $email, $user_id])) {
                $_SESSION['fullname'] = $fullname;
                $success_msg = "Xogta profile-kaaga Admin ahaan si guul leh ayaa loo beddelay!";
                $stmt->execute([$user_id]);
                $current_user = $stmt->fetch();
            } else {
                $error_msg = "Waqti xaadirkan laguma guulaysan isbeddelka profile-ka.";
            }
        }
    }
    
    // ACTION B: Admin-ka oo shaqaale/user cusub ku daraya nidaamka (ADD USER)
    if (isset($_POST['add_new_user'])) {
        $new_fullname = trim($_POST['new_fullname']);
        $new_email = trim($_POST['new_email']);
        $new_password = $_POST['new_password']; // Ama password_hash adaa dooran kara
        $new_role = $_POST['new_role'];
        
        if (!empty($new_fullname) && !empty($new_email) && !empty($new_password) && !empty($new_role)) {
            // Hubi in email-kaas horey loo haysto
            $check_email = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
            $check_email->execute([$new_email]);
            
            if ($check_email->fetchColumn() > 0) {
                $error_msg = "Email-kan horey ayaa nidaamka loogu diiwaangeliyey!";
            } else {
                // Toos ugu dar database-ka
                $insert_user = $pdo->prepare("INSERT INTO users (fullname, email, password, role) VALUES (?, ?, ?, ?)");
                if ($insert_user->execute([$new_fullname, $new_email, $new_password, $new_role])) {
                    $success_msg = "User cusub ($new_fullname) oo leh doorka [$new_role] si guul leh ayaa nidaamka loogu daray!";
                } else {
                    $error_msg = "Laguma guulaysan diiwaangelinta isticmaalaha cusub.";
                }
            }
        } else {
            $error_msg = "Fadlan wada buuxi foomka user-ka cusub!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="so">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VetExpert - Admin Settings & Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="sidebar">
    <div class="sidebar-brand">
        <h4>VetExpert</h4>
        <small>Regulatory System</small>
    </div>
    <div class="mt-4">
        <a href="../dashboard/dashboard.php"><i class="bi bi-grid-fill"></i> Dashboard</a>
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
            <a href="index.php" class="active"><i class="bi bi-gear-fill"></i> Settings</a>
        <?php endif; ?>
    </div>
    <div class="sidebar-footer">
        <a href="../index.php" class="text-danger"><i class="bi bi-box-arrow-left text-danger"></i> Sign Out</a>
    </div>
</div>

<div class="main-content">
    
    <div class="topbar">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-shield-lock-fill fs-4 text-success"></i>
            <h4 class="m-0" style="color: #1b4324; font-weight:700;">Admin Control Panel</h4>
        </div>
        <div class="d-flex align-items-center gap-4">
            <div class="d-flex align-items-center gap-2">
                <div class="text-end">
                    <div class="fw-bold" style="font-size:13px;"><?= htmlspecialchars($_SESSION['fullname'] ?? 'Admin'); ?></div>
                    <span class="badge bg-danger" style="font-size:10px;"><?= $role; ?> Panel</span>
                </div>
                <i class="bi bi-person-badge-fill fs-3 text-success"></i>
            </div>
        </div>
    </div>

    <div class="container-fluid p-4">
        
        <!-- Alerts -->
        <?php if(!empty($success_msg)): ?>
            <div class="alert alert-success d-flex align-items-center gap-2 shadow-sm border-0"><i class="bi bi-check-circle-fill"></i> <?= $success_msg; ?></div>
        <?php endif; ?>
        <?php if(!empty($error_msg)): ?>
            <div class="alert alert-danger d-flex align-items-center gap-2 shadow-sm border-0"><i class="bi bi-exclamation-triangle-fill"></i> <?= $error_msg; ?></div>
        <?php endif; ?>

        <div class="row g-4">
            
            <!-- QAYBTA 1: KU DARISTA USER CUSUB (Add New User) -->
            <div class="col-md-7">
                <div class="card border-0 shadow-sm p-4">
                    <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-person-plus-fill text-success me-2"></i> Register New Authority User</h5>
                    <p class="text-muted" style="font-size:13px;">Halkan uga samee akoon cusub saraakiisha caafimaadka (Vets) ama saraakiisha dhoofka (Export Officers).</p>
                    <hr class="text-muted">
                    
                    <form action="" method="POST">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label text-secondary fw-semibold">Full Name</label>
                                <input type="text" name="new_fullname" class="form-control py-2" placeholder="Saciid Cali" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-secondary fw-semibold">Email Address</label>
                                <input type="email" name="new_email" class="form-control py-2" placeholder="saciid@authority.gov" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-secondary fw-semibold">Temporary Password</label>
                                <input type="password" name="new_password" class="form-control py-2" placeholder="••••••••" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-secondary fw-semibold">System Access Role</label>
                                <select name="new_role" class="form-select py-2" required>
                                    <option value="" selected disabled>Dooro Doorka User-ka...</option>
                                    <option value="Veterinary Officer">Veterinary Officer (Dhakhtar)</option>
                                    <option value="Export Officer">Export Officer (Sarkaalka Dhoofka)</option>
                                    <option value="Admin">System Administrator</option>
                                </select>
                            </div>
                        </div>
                        <button type="submit" name="add_new_user" class="btn btn-success px-4 py-2 mt-4 w-100" style="background-color: #1b4324; border:none;">
                            <i class="bi bi-person-check-fill me-2"></i> Diiwaangeli User-ka Cusub
                        </button>
                    </form>
                </div>
            </div>

            <!-- QAYBTA 2: PROFILE-KA ADMIN-KA IDIISA -->
            <div class="col-md-5">
                <div class="card border-0 shadow-sm p-4 h-100">
                    <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-sliders text-dark me-2"></i> My Admin Account</h5>
                    <p class="text-muted" style="font-size:13px;">Beddelista macluumaadka akoonkaada maamulnimo.</p>
                    <hr class="text-muted">
                    
                    <form action="" method="POST">
                        <div class="mb-3">
                            <label class="form-label text-secondary fw-semibold">Admin Full Name</label>
                            <input type="text" name="fullname" class="form-control py-2" value="<?= htmlspecialchars($current_user['fullname']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-secondary fw-semibold">Admin Email</label>
                            <input type="email" name="email" class="form-control py-2" value="<?= htmlspecialchars($current_user['email']); ?>" required>
                        </div>
                        <button type="submit" name="update_profile" class="btn btn-dark px-4 py-2 w-100 mt-2">Cusboonaysii Akoonkaaga</button>
                    </form>
                </div>
            </div>

            <!-- Qaybta 3: Xogta System Operational Node -->
            <div class="col-12">
                <div class="card border-0 shadow-sm p-4" style="background: #f8fafc; border-left: 4px solid #1b4324 !important;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="m-0 fw-bold text-dark"><i class="bi bi-shield-check text-success me-2"></i> Access Level Locked to Administrator</h6>
                            <small class="text-muted">Users created through this portal automatically sync with the live database <strong>users</strong> table.</small>
                        </div>
                        <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 fw-bold">SECURED NODE</span>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <div class="bottom-status-bar">
        <div>● System Security Profile: Active Node &nbsp;|&nbsp; Location: Hargeisa</div>
        <div>Authorized Mode: <strong><?= strtoupper($role); ?> PANEL</strong></div>
    </div>
</div>

</body>
</html>