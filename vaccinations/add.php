<?php
session_start();
require_once '../includes/db.php';

// Strict Authentication Validation Loop
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

// Multi-Tier RBAC Access Verification Layer
if ($_SESSION['role'] !== 'Admin' && $_SESSION['role'] !== 'Veterinary Officer') {
    die("<div style='font-family:sans-serif; text-align:center; margin-top:100px; color:#dc3545;'>
            <i class='bi bi-shield-lock-fill' style='font-size: 3rem;'></i>
            <h3 style='margin-top:20px;'>Awood-diidmo: Sarkaalka dhoofku ma qori karo tallaalka.</h3>
            <p style='color:#6c757d;'>Kaliya Maamulayaasha iyo Dhakhaatiirta Xoolaha ayaa geli kara boggan.</p>
            <a href='../dashboard/dashboard.php' style='color:#2e7d32; font-weight:bold; text-decoration:none;'>Ku laabo Dashboard-ka</a>
         </div>");
}

$error_msg = "";
$animals = $pdo->query("SELECT id, animal_id, species FROM animals ORDER BY id DESC")->fetchAll();

// Cryptographic Cryptotoken Handshake Generation for Anti-CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate Token Authenticity 
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Security Exception: CSRF verification token is invalid or expired.");
    }

    $animal_id     = filter_input(INPUT_POST, 'animal_id', FILTER_VALIDATE_INT);
    $vaccine_name  = filter_input(INPUT_POST, 'vaccine_name', FILTER_SANITIZE_SPECIAL_CHARS);
    $batch_number  = filter_input(INPUT_POST, 'batch_number', FILTER_SANITIZE_SPECIAL_CHARS);
    $vaccinated_at = $_POST['vaccinated_at'] ?? '';
    $expiry_date   = $_POST['expiry_date'] ?? '';

    if (!empty($animal_id) && !empty($vaccine_name) && !empty($vaccinated_at)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO vaccinations (animal_id, vaccine_name, batch_number, vaccinated_at, expiry_date) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$animal_id, $vaccine_name, $batch_number, $vaccinated_at, $expiry_date]);
            
            // Clean up CSRF Token on successful post operation transaction
            unset($_SESSION['csrf_token']);
            header("Location: index.php?success=VaccineRecorded");
            exit;
        } catch (PDOException $e) {
            $error_msg = "Qalad ayaa ka dhacay kaydinta: " . $e->getMessage();
        }
    } else {
        $error_msg = "Fadlan wada buuxi meelaha muhiimka ah ee calaamadsan!";
    }
}
?>
<!DOCTYPE html>
<html lang="so">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Record Vaccine - VetExpert</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <style>
        body {
            background-color: #eff2f0; 
           
            background-size: 100% 100%, 100% 100%, 25px 25px, 25px 25px;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .pro-card {
            background: rgba(255, 255, 255, 0.98);
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4) !important;
            border-top: 5px solid #2e7d32 !important;
            backdrop-filter: blur(10px);
        }
        .form-floating > .form-control:focus ~ label,
        .form-floating > .form-control:not(:placeholder-shown) ~ label,
        .form-floating > .form-select ~ label {
            color: #2e7d32;
        }
    </style>
</head>
<body>

    <div class="container my-5" style="max-width: 580px;">
        
        <?php if(!empty($error_msg)): ?>
            <div class="alert alert-danger d-flex align-items-center gap-2 border-0 shadow-sm mb-3" style="border-radius: 10px;">
                <i class="bi bi-exclamation-triangle-fill"></i> <?= $error_msg; ?>
            </div>
        <?php endif; ?>

        <div class="card pro-card p-4 p-md-5 border-0">
            <div class="d-flex align-items-center gap-3 mb-2">
                <div class="bg-success bg-opacity-10 p-2 rounded-3 text-success">
                    <i class="bi bi-shield-plus fs-3"></i>
                </div>
                <div>
                    <h4 class="m-0 text-dark fw-bold" style="letter-spacing: -0.5px;">Record Vaccination Matrix</h4>
                    <p class="text-muted small mb-0">Diiwaangeli tallaalka rasmiga ah ee la siiyey neefka.</p>
                </div>
            </div>
            <hr class="text-muted my-4">
            
            <form method="POST" id="vaccineForm" novalidate>
                <!-- CSRF Protection Security Token -->
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
                
                <!-- Floating Target Animal Selection -->
                <div class="form-floating mb-3">
                    <select name="animal_id" id="animalSelect" class="form-select rounded-3" required>
                        <option value="" selected disabled>-- Dooro Xoolaha --</option>
                        <?php foreach ($animals as $animal): ?>
                            <option value="<?= $animal['id']; ?>">
                                #<?= htmlspecialchars($animal['animal_id']); ?> (<?= htmlspecialchars($animal['species']); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <label for="animalSelect"><i class="bi bi-tag-fill me-1"></i> Target Animal Assets (RFID ID) *</label>
                </div>
                
                <!-- Floating Vaccine Name/Type -->
                <div class="form-floating mb-3">
                    <input type="text" name="vaccine_name" id="vacNameInput" class="form-control rounded-3" placeholder="PPR Vaccine" required>
                    <label for="vacNameInput"><i class="bi bi-capsule me-1"></i> Vaccine Name / Type Code *</label>
                </div>

                <!-- Floating Batch Serial Number -->
                <div class="form-floating mb-3">
                    <input type="text" name="batch_number" id="batchInput" class="form-control rounded-3 fw-bold text-secondary" placeholder="BATCH-2026X" required>
                    <label for="batchInput"><i class="bi bi-hash me-1"></i> Vaccine Batch / Lot Serial Number *</label>
                </div>
                
                <!-- Date Processing Clusters -->
                <div class="row g-3 mb-4">
                    <div class="col-6">
                        <div class="form-floating">
                            <input type="date" name="vaccinated_at" id="vacDate" class="form-control rounded-3" value="<?= date('Y-m-d'); ?>" required>
                            <label for="vacDate"><i class="bi bi-calendar-event me-1"></i> Vaccination Date</label>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-floating">
                            <input type="date" name="expiry_date" id="expDate" class="form-control rounded-3" value="<?= date('Y-m-d', strtotime('+1 year')); ?>" required>
                            <label for="expDate"><i class="bi bi-calendar-x me-1"></i> Expiry Date</label>
                        </div>
                    </div>
                </div>
                
                <!-- Action Execution Buttons Grid -->
                <div class="row g-3 pt-2">
                    <div class="col-md-8">
                        <button type="submit" id="submitBtn" class="btn btn-success py-3 w-100 fw-bold shadow-sm rounded-3 bg-success border-0">
                            <span id="btnText"><i class="bi bi-cloud-arrow-up-fill me-1"></i> Save Vaccination Record</span>
                        </button>
                    </div>
                    <div class="col-md-4">
                        <a href="index.php" class="btn btn-light py-3 w-100 fw-bold border rounded-3 text-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Back
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Preventing duplicate data submissions natively
        document.getElementById('vaccineForm').addEventListener('submit', function() {
            document.getElementById('submitBtn').disabled = true;
            document.getElementById('btnText').textContent = "Processing security ledger transactional data...";
        });
    </script>
</body>
</html>
                  