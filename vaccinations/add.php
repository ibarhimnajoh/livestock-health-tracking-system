<?php
session_start();
require_once '../includes/db.php';

// Amniga: Hubi in qofku soo galay nidaamka
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/index.php");
    exit;
}

// Hubi awoodda isticmaalaha (Kaliya Admin iyo Vet Officer ayaa geli kara)
if ($_SESSION['role'] !== 'Admin' && $_SESSION['role'] !== 'Veterinary Officer') {
    die("<div style='font-family:sans-serif; text-align:center; margin-top:50px; color:#dc3545;'><h3>Awood-diidmo: Sarkaalka dhoofku ma qori karo tallaalka.</h3><a href='../dashboard/dashboard.php'>Ku laabo Dashboard-ka</a></div>");
}

$error_msg = "";
$animals = $pdo->query("SELECT id, animal_id, species FROM animals ORDER BY id DESC")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $animal_id     = $_POST['animal_id'];
    $vaccine_name  = trim($_POST['vaccine_name']);
    $batch_number  = trim($_POST['batch_number']);
    $vaccinated_at = $_POST['vaccinated_at'];
    $expiry_date   = $_POST['expiry_date'];

    if (!empty($animal_id) && !empty($vaccine_name) && !empty($vaccinated_at)) {
        try {
            // Ku rid miiska rasmiga ah ee database-ka
            $stmt = $pdo->prepare("INSERT INTO vaccinations (animal_id, vaccine_name, batch_number, vaccinated_at, expiry_date) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$animal_id, $vaccine_name, $batch_number, $vaccinated_at, $expiry_date]);
            
            header("Location: index.php?success=VaccineRecorded");
            exit;
        } catch (PDOException $e) {
            $error_msg = "Qalad ayaa ka dhacay kaydinta: " . $e->getMessage();
        }
    } else {
        $error_msg = "Fadlan wada buuxi meelaha muhiimka ah!";
    }
}
?>
<!DOCTYPE html>
<html lang="so">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Record Vaccine - VetExpert</title>
    <!-- Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <style>
        /* PRO BACKGROUND STYLE */
        body {
            background-color: #0f2917; /* Midab madow cagaar xiga */
            background-image: 
                radial-gradient(circle at 20% 30%, rgba(27, 67, 36, 0.6) 0%, transparent 50%),
                radial-gradient(circle at 80% 70%, rgba(46, 125, 50, 0.4) 0%, transparent 50%),
                linear-gradient(rgba(27, 67, 36, 0.1) 2px, transparent 2px), 
                linear-gradient(90deg, rgba(27, 67, 36, 0.1) 2px, transparent 2px);
            background-size: 100% 100%, 100% 100%, 25px 25px, 25px 25px;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        /* CARD MODIFICATIONS */
        .pro-card {
            background: rgba(255, 255, 255, 0.98);
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4) !important;
            border-top: 5px solid #2e7d32 !important;
            backdrop-filter: blur(10px);
        }
        
        .form-label {
            color: #2c3e50;
            font-size: 13px;
        }
        
        .form-control, .form-select {
            border: 1px solid #ced4da;
            padding: 10px 12px;
            font-size: 14px;
            border-radius: 8px;
            transition: all 0.2s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #2e7d32;
            box-shadow: 0 0 0 0.25rem rgba(46, 125, 50, 0.25);
        }
        
        .btn-success-pro {
            background-color: #1b4324;
            color: white;
            border: none;
            padding: 11px;
            border-radius: 8px;
            font-size: 14px;
            transition: background 0.2s;
        }
        
        .btn-success-pro:hover {
            background-color: #285c33;
            color: white;
        }

        .btn-secondary-pro {
            background-color: #e2e8f0;
            color: #475569;
            border: none;
            padding: 11px;
            border-radius: 8px;
            font-size: 14px;
            text-decoration: none;
            text-align: center;
            transition: all 0.2s;
        }
        
        .btn-secondary-pro:hover {
            background-color: #cbd5e1;
            color: #1e293b;
        }
    </style>
</head>
<body>

    <div class="container my-5" style="max-width: 550px;">
        
        <!-- Qaybta Digniinta haddii qalad jiro -->
        <?php if(!empty($error_msg)): ?>
            <div class="alert alert-danger d-flex align-items-center gap-2 border-0 shadow-sm mb-3" style="border-radius: 10px;">
                <i class="bi bi-exclamation-triangle-fill"></i> <?= $error_msg; ?>
            </div>
        <?php endif; ?>

        <div class="card pro-card p-4 p-md-5">
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="bi bi-shield-plus text-success fs-3"></i>
                <h4 class="m-0 text-dark fw-bold" style="letter-spacing: -0.5px;">Record Vaccination</h4>
            </div>
            <p class="text-muted small mb-4">Diiwaangeli tallaalka rasmiga ah ee la siiyey xoolaha nidaamka ku jira.</p>
            
            <form method="POST">
                
                <!-- Target Animal (RFID) -->
                <div class="mb-3">
                    <label class="form-label fw-semibold"><i class="bi bi-tag-fill text-secondary me-1"></i> Target Animal (RFID ID)</label>
                    <select name="animal_id" class="form-select" required>
                        <option value="" selected disabled>-- Dooro Xoolaha --</option>
                        <?php foreach ($animals as $animal): ?>
                            <option value="<?= $animal['id']; ?>">
                                #<?= htmlspecialchars($animal['animal_id']); ?> (<?= htmlspecialchars($animal['species']); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Vaccine Name -->
                <div class="mb-3">
                    <label class="form-label fw-semibold"><i class="bi bi-capsule text-secondary me-1"></i> Vaccine Name / Type</label>
                    <input type="text" name="vaccine_name" class="form-control" placeholder="Tusaale: PPR Vaccine, CCPP, FMD" required>
                </div>

                <!-- Batch Number -->
                <div class="mb-3">
                    <label class="form-label fw-semibold"><i class="bi bi-hash text-secondary me-1"></i> Vaccine Batch / Serial Number</label>
                    <input type="text" name="batch_number" class="form-control" placeholder="Tusaale: BATCH-2026X" required>
                </div>
                
                <!-- Dates Row -->
                <div class="row g-3 mb-4">
                    <div class="col-6">
                        <label class="form-label fw-semibold"><i class="bi bi-calendar-event text-secondary me-1"></i> Vaccination Date</label>
                        <input type="date" name="vaccinated_at" class="form-control" value="<?= date('Y-m-d'); ?>" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold"><i class="bi bi-calendar-x text-secondary me-1"></i> Expiry Date</label>
                        <input type="date" name="expiry_date" class="form-control" value="<?= date('Y-m-d', strtotime('+1 year')); ?>" required>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="row g-2 pt-2">
                    <div class="col-8">
                        <button type="submit" class="btn btn-success-pro w-100 fw-bold shadow-sm">
                            <i class="bi bi-cloud-arrow-up-fill me-1"></i> Save Record
                        </button>
                    </div>
                    <div class="col-4">
                        <a href="index.php" class="btn btn-secondary-pro w-100 fw-bold">
                            <i class="bi bi-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

</body>
</html>