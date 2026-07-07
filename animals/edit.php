<?php
session_start();
require_once '../includes/db.php';

// 1. Amniga doorka: Kaliya Admin iyo Vet ayaa wax beddeli kara
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'Admin' && $_SESSION['role'] !== 'Veterinary Officer')) {
    die("Awood uma lihid inaad aragto boggan.");
}

$id = $_GET['id'] ?? null;
if (!$id) { 
    header("Location: index.php"); 
    exit; 
}

// 2. Soo qaad xogta neefka hadda dhex ku jira database-ka
$stmt = $pdo->prepare("SELECT * FROM animals WHERE id = ?");
$stmt->execute([$id]);
$animal = $stmt->fetch();

if (!$animal) { 
    die("Xogta neefkan laguma helin database-ka."); 
}

// Soo qaad liiska milkiilayaasha si loogu xusho foomka dhexdiisa
$owners = $pdo->query("SELECT id, name FROM owners ORDER BY name ASC")->fetchAll();

// 3. Marka badhanka Save Changes la gujiyo (POST Request)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $animal_id   = $_POST['animal_id'] ?? '';
    $owner_id    = $_POST['owner_id'] ?? '';
    $species     = $_POST['species'] ?? '';
    $breed       = $_POST['breed'] ?? '';
    $age_months  = $_POST['age_months'] ?? 0;
    $gender      = $_POST['gender'] ?? '';
    $status      = $_POST['status'] ?? '';

    if (!empty($animal_id) && !empty($owner_id) && !empty($status)) {
        // Cusboonaysiinta miiska animals
        $update = $pdo->prepare("UPDATE animals SET animal_id = ?, owner_id = ?, species = ?, breed = ?, age_months = ?, gender = ?, status = ? WHERE id = ?");
        $update->execute([$animal_id, $owner_id, $species, $breed, $age_months, $gender, $status, $id]);
        
        header("Location: index.php?success=AnimalUpdated");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="so">
<head>
    <meta charset="UTF-8">
    <title>Edit Animal - VetExpert</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-5">

    <div class="container" style="max-width: 600px;">
        <div class="card shadow-sm border-0 p-4">
            <h4 class="mb-3 text-dark fw-bold">Edit Animal Profile</h4>
            <hr class="mb-4">
            
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label fw-semibold">RFID / Tag ID</label>
                    <input type="text" name="animal_id" class="form-control" value="<?= htmlspecialchars($animal['animal_id'] ?? ''); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Assign Owner</label>
                    <select name="owner_id" class="form-select" required>
                        <?php foreach ($owners as $owner): ?>
                            <option value="<?= $owner['id']; ?>" <?= $animal['owner_id'] == $owner['id'] ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($owner['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Species</label>
                        <select name="species" class="form-select" required>
                            <option value="Bovine" <?= $animal['species'] == 'Bovine' ? 'selected' : ''; ?>>Bovine (Lo')</option>
                            <option value="Ovine" <?= $animal['species'] == 'Ovine' ? 'selected' : ''; ?>>Ovine (Ido)</option>
                            <option value="Cameline" <?= $animal['species'] == 'Cameline' ? 'selected' : ''; ?>>Cameline (Geel)</option>
                            <option value="Caprine" <?= $animal['species'] == 'Caprine' ? 'selected' : ''; ?>>Caprine (Ari)</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Breed</label>
                        <input type="text" name="breed" class="form-control" value="<?= htmlspecialchars($animal['breed'] ?? ''); ?>">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Age (Months)</label>
                        <input type="number" name="age_months" class="form-control" value="<?= htmlspecialchars($animal['age_months'] ?? 0); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Gender</label>
                        <select name="gender" class="form-select" required>
                            <option value="Male" <?= $animal['gender'] == 'Male' ? 'selected' : ''; ?>>Male</option>
                            <option value="Female" <?= $animal['gender'] == 'Female' ? 'selected' : ''; ?>>Female</option>
                        </select>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="form-label fw-semibold">Health Status</label>
                    <select name="status" class="form-select" required>
                        <option value="Healthy" <?= $animal['status'] == 'Healthy' ? 'selected' : ''; ?>>Healthy</option>
                        <option value="Quarantined" <?= $animal['status'] == 'Quarantined' ? 'selected' : ''; ?>>Quarantined</option>
                        <option value="Treatment" <?= $animal['status'] == 'Treatment' ? 'selected' : ''; ?>>Under Treatment</option>
                        <option value="Exported" <?= $animal['status'] == 'Exported' ? 'selected' : ''; ?>>Exported</option>
                    </select>
                </div>
                
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success w-100 fw-bold">Save Changes</button>
                    <a href="index.php" class="btn btn-secondary w-100 fw-bold">Cancel</a>
                </div>
            </form>
        </div>
    </div>

</body>
</html>