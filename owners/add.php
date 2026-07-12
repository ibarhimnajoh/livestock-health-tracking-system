<?php
session_start();

// Strict Role-Based Access Control (RBAC) Encryption Layer
if (!isset($_SESSION['user_id'])) { 
    header("Location: ../index.php"); 
    exit; 
}

$role = $_SESSION['role'] ?? 'Export Officer';
if ($role !== 'Admin' && $role !== 'Veterinary Officer') {
    header("Location: index.php?error=unauthorized");
    exit;
}

require_once '../includes/db.php';

// Generate Cryptographically Secure CSRF Token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate CSRF Anti-Forgery Token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Security Violation: CSRF token validation failed.");
    }

    // Input Sanitization Filtering
    $name    = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
    $phone   = trim($_POST['phone']);
    $email   = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $region  = filter_input(INPUT_POST, 'region', FILTER_SANITIZE_SPECIAL_CHARS);
    $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_SPECIAL_CHARS);

    // Strict Backend Validation Rules
    if (empty($name) || empty($phone) || empty($region)) {
        $error = "Fadlan buuxi dhammaan meelaha muhiimka ah ee calaamadaysan.";
    } elseif (!preg_match('/^(63|65|61|90|\+?252)?[0-9\-\s]{7,15}$/', $phone)) {
        $error = "Nambarka taleefanku uma dhigmo qaabka saxda ah ee nidaamka.";
    } elseif (!empty($_POST['email']) && !$email) {
        $error = "Ciwaanka email-ka aad gelisay ma aha mid shaqaynaya.";
    } else {
        try {
            // Check for Duplicate Phone/Email to maintain Data Integrity
            $checkStmt = $pdo->prepare("SELECT id FROM owners WHERE phone = :phone OR (email = :email AND email IS NOT NULL)");
            $checkStmt->execute([':phone' => $phone, ':email' => $email ? $email : null]);
            
            if ($checkStmt->fetch()) {
                $error = "Milkiile wata taleefankan ama email-kan ayaa hore nidaamka ugu jira.";
            } else {
                // Execute Secure Parameterized Insert Transaction
                $stmt = $pdo->prepare("INSERT INTO owners (name, phone, email, region, address) VALUES (:name, :phone, :email, :region, :address)");
                $stmt->execute([
                    ':name'    => ucwords(strtolower($name)), // Normalize text case
                    ':phone'   => $phone,
                    ':email'   => $email ? strtolower($email) : null,
                    ':region'  => $region,
                    ':address' => $address
                ]);
                
                unset($_SESSION['csrf_token']); // Rotate CSRF token on success
                header("Location: index.php?success=added");
                exit;
            }
        } catch (PDOException $e) {
            $error = "System Query Failure: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="so">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enterprise Owner Provisioning - VetExpert</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .form-floating>.form-control:focus~label, .form-floating>.form-control:not(:placeholder-shown)~label, .form-floating>.form-select~label {
            color: #198754;
        }
        .card-custom { border-radius: 16px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05); }
    </style>
</head>
<body class="bg-light py-5">

<div class="container" style="max-width: 700px;">
    <!-- Context Header -->
    <div class="text-center mb-4">
        <h3 class="fw-bold text-dark mb-1">VetExpert Orchestration Portal</h3>
        <p class="text-muted small">Diiwaangelinta xogta rasmiga ah ee milkiilayaasha xoolaha dhoofaya.</p>
    </div>

    <div class="card card-custom border-0 bg-white overflow-hidden">
        <div class="card-header bg-dark text-white p-4 border-0 d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <div class="bg-success p-2 rounded-3 me-3 text-white">
                    <i class="bi bi-person-plus-fill fs-5"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold">New Owner Profile</h5>
                    <small class="text-muted">Account Tier: <?= htmlspecialchars($role); ?></small>
                </div>
            </div>
            <a href="index.php" class="btn btn-sm btn-outline-light rounded-pill px-3"><i class="bi bi-list-task me-1"></i> View All</a>
        </div>
        
        <div class="card-body p-4">
            <!-- Server-Side Exception Toast/Alert -->
            <?php if($error): ?> 
                <div class="alert alert-danger border-0 shadow-sm d-flex align-items-center rounded-3 mb-4" role="alert">
                    <i class="bi bi-shield-exclamation fs-4 me-3 text-danger"></i>
                    <div class="fw-semibold text-dark"><?= $error; ?></div>
                </div> 
            <?php endif; ?>

            <form action="" method="POST" id="ownerRegistrationForm" autocomplete="off" novalidate>
                <!-- CSRF Token Layer -->
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">

                <!-- Floating Name Input -->
                <div class="form-floating mb-3">
                    <input type="text" name="name" id="ownerName" class="form-control rounded-3" placeholder="Full Name" value="<?= isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required>
                    <label for="ownerName">Magaca Aqoonsiga oo Buuxa (Full Name) *</label>
                    <div class="invalid-feedback">Fadlan geli magac sax ah oo ka kooban ugu yaraan 3 xaraf.</div>
                </div>

                <div class="row g-3 mb-3">
                    <!-- Floating Phone Input -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="tel" name="phone" id="ownerPhone" class="form-control rounded-3" placeholder="Phone" value="<?= isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>" required>
                            <label for="ownerPhone">Nambarka Taleefanka *</label>
                            <div class="invalid-feedback">Geli nambar taleefan oo sax ah (Ex: 63XXXXXXX).</div>
                        </div>
                    </div>
                    <!-- Floating Region Dropdown -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select name="region" id="ownerRegion" class="form-select rounded-3" required>
                                <option value="" disabled selected>Dooro Gobolka...</option>
                                <?php 
                                $regions = ["Maroodi Jeex", "Bari", "Nugaal", "Togdheer", "Awdal", "Sanaag", "Sool"];
                                foreach($regions as $r): ?>
                                    <option value="<?= $r; ?>" <?= (isset($_POST['region']) && $_POST['region'] == $r) ? 'selected' : ''; ?>><?= $r; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label for="ownerRegion">Gobolka (Geographic Region) *</label>
                            <div class="invalid-feedback">Fadlan xulu barta gobolka uu farm-ku ku yaallo.</div>
                        </div>
                    </div>
                </div>

                <!-- Floating Email Input -->
                <div class="form-floating mb-3">
                    <input type="email" name="email" id="ownerEmail" class="form-control rounded-3" placeholder="name@example.com" value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    <label for="ownerEmail">Ciwaanka Email-ka (Optional)</label>
                    <div class="invalid-feedback">Qaabka email-ku ma sanna. Ka tag maran ama sax.</div>
                </div>

                <!-- Floating Address Textarea -->
                <div class="form-floating mb-4">
                    <textarea name="address" id="ownerAddress" class="form-control rounded-3" style="height: 100px" placeholder="Address"><?= isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?></textarea>
                    <label for="ownerAddress">Goobta rasmiga ah ee Farm-ka (Specific Farm Location / Address)</label>
                </div>

                <!-- Interface Control Buttons -->
                <div class="d-flex justify-content-between align-items-center border-top pt-4">
                    <a href="index.php" class="btn btn-light border rounded-pill px-4 py-2"><i class="bi bi-x-circle me-1"></i> Ka Noqo</a>
                    <button type="submit" id="submitBtn" class="btn btn-success rounded-pill px-5 py-2 fw-bold shadow-sm">
                        <span id="btnText"><i class="bi bi-cloud-arrow-up-fill me-1"></i> Kaydi Milkiilaha</span>
                        <span id="btnSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Advanced Client-Side Validation & Security Engine -->
<script>
    document.getElementById('ownerRegistrationForm').addEventListener('submit', function(e) {
        let form = this;
        let name = document.getElementById('ownerName');
        let phone = document.getElementById('ownerPhone');
        let region = document.getElementById('ownerRegion');
        let email = document.getElementById('ownerEmail');
        let isValid = true;

        // Name verification
        if (name.value.trim().length < 3) {
            name.classList.add('is-invalid');
            isValid = false;
        } else {
            name.classList.remove('is-invalid');
            name.classList.add('is-valid');
        }

        // Phone pattern validation
        let phoneRegex = /^(63|65|61|90|\+?252)?[0-9\-\s]{7,15}$/;
        if (!phoneRegex.test(phone.value.trim())) {
            phone.classList.add('is-invalid');
            isValid = false;
        } else {
            phone.classList.remove('is-invalid');
            phone.classList.add('is-valid');
        }

        // Region tracking selection
        if (region.value === "") {
            region.classList.add('is-invalid');
            isValid = false;
        } else {
            region.classList.remove('is-invalid');
            region.classList.add('is-valid');
        }

        // Optional Email validator
        if (email.value.trim() !== "") {
            let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email.value.trim())) {
                email.classList.add('is-invalid');
                isValid = false;
            } else {
                email.classList.remove('is-invalid');
                email.classList.add('is-valid');
            }
        }

        if (!isValid) {
            e.preventDefault(); // Stop processing structural request
        } else {
            // Trigger Dual-Submit Prevention UX Flow
            document.getElementById('submitBtn').disabled = true;
            document.getElementById('btnText').textContent = "Kaydinta xogta...";
            document.getElementById('btnSpinner').classList.remove('d-none');
        }
    });

    // Real-time Input Validation Cleanups
    document.querySelectorAll('.form-control, .form-select').forEach(element => {
        element.addEventListener('input', function() {
            if(this.checkValidity()) {
                this.classList.remove('is-invalid');
            }
        });
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>