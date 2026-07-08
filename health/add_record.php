<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] == 'Export Officer') { 
    die("Ma lihid ogolaansho aad ku gasho boggan."); 
}
require_once '../includes/db.php';

$animal_db_id = isset($_GET['animal_id']) ? intval($_GET['animal_id']) : 0;

// Hubi in neefku jiro
$stmt = $pdo->prepare("SELECT * FROM animals WHERE id = ?");
$stmt->execute([$animal_db_id]);
$animal = $stmt->fetch();
if (!$animal) { die("Xoolo helmi waayay."); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $diagnosis = trim($_POST['diagnosis']);
    $status = $_POST['status'];
    $inspection_date = $_POST['inspection_date'];
    $vet_id = $_SESSION['user_id'];

    $pdo->beginTransaction();
    try {
        // 1. Geli Health Record table
        $stmt = $pdo->prepare("INSERT INTO health_records (animal_id, vet_id, diagnosis, status, inspection_date) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$animal_db_id, $vet_id, $diagnosis, $status, $inspection_date]);

        // 2. Cusboonaysii Status-ka neefka ee miiska animals
        $stmtUpdate = $pdo->prepare("UPDATE animals SET status = ? WHERE id = ?");
        $stmtUpdate->execute([$status, $animal_db_id]);

        $pdo->commit();
        header("Location: index.php");
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Cillad ayaa dhacday: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="so">
<head>
    <meta charset="UTF-8">
    <title>Record Health Status</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-5">

<div class="container" style="max-width: 550px;">
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <h4 class="mb-3">Health Inspection for #<?= htmlspecialchars($animal['animal_id']); ?></h4>
            <p class="text-muted">Nooca: <?= $animal['species']; ?></p>
            
            <form action="" method="POST">
                <div class="mb-3">
                    <label class="form-label">Diagnosis Findings (Wixii lagu arkay)</label>
                    <textarea name="diagnosis" class="form-control" rows="3" required placeholder="Tusaale: Neefku waa caafimaad qabaa, calaamado xanuun lama arag."></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Health Status Decision</label>
                    <select name="status" class="form-select" required>
                        <option value="Healthy">Healthy (Waa Caafimaad)</option>
                        <option value="Quarantined">Quarantined (Karantil Geli)</option>
                        <option value="Treatment">Treatment (Daaweyn)</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label">Inspection Date</label>
                    <input type="date" name="inspection_date" class="form-control" value="<?= date('Y-m-d'); ?>" required>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="../animals/index.php" class="btn btn-light">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save Inspection</button>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>