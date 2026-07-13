<?php
session_start();
require_once '../includes/db.php';

// Strict RBAC Authentication
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'Admin' && $_SESSION['role'] !== 'Veterinary Officer')) {
    die("Security Violation: Access Denied.");
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) { 
    header("Location: index.php"); 
    exit; 
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$stmt = $pdo->prepare("SELECT * FROM animals WHERE id = ?");
$stmt->execute([$id]);
$animal = $stmt->fetch();

if (!$animal) { 
    die("Error: Asset record missing from schema context."); 
}

$owners = $pdo->query("SELECT id, name FROM owners ORDER BY name ASC")->fetchAll();
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Security Exception: CSRF handshake invalid.");
    }

    $animal_id   = filter_input(INPUT_POST, 'animal_id', FILTER_SANITIZE_SPECIAL_CHARS);
    $owner_id    = filter_input(INPUT_POST, 'owner_id', FILTER_VALIDATE_INT);
    $species     = $_POST['species'] ?? '';
    $breed       = filter_input(INPUT_POST, 'breed', FILTER_SANITIZE_SPECIAL_CHARS);
    $age_months  = filter_input(INPUT_POST, 'age_months', FILTER_VALIDATE_INT);
    $gender      = $_POST['gender'] ?? '';
    $status      = $_POST['status'] ?? '';

    if (!empty($animal_id) && !empty($owner_id) && !empty($status)) {
        try {
            $update = $pdo->prepare("UPDATE animals SET animal_id = ?, owner_id = ?, species = ?, breed = ?, age_months = ?, gender = ?, status = ? WHERE id = ?");
            $update->execute([$animal_id, $owner_id, $species, $breed, $age_months, $gender, $status, $id]);
            
            unset($_SESSION['csrf_token']);
            header("Location: index.php?success=AnimalUpdated");
            exit;
        } catch(PDOException $e) {
            $error = "Execution Framework Failure: " . $e->getMessage();
        }
    } else {
        $error = "Fadlan buuxi meelaha muhiimka ah ee calaamadsan.";
    }
}
?>
<!DOCTYPE html>
<html lang="so">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Asset Profile - VetExpert</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .card-custom { border-radius: 16px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05); }
    </style>
</head>
<body class="bg-light py-5">

<div class="container" style="max-width: 650px;">
    <div class="card card-custom border-0 bg-white overflow-hidden">
        <div class="card-header bg-dark text-white p-4 border-0">
            <div class="d-flex align-items-center">
                <div class="bg-primary p-2 rounded-3 me-3 text-white">
                    <i class="bi bi-pencil-square fs-5"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold">Modify Animal Matrix</h5>
                    <small class="text-muted">Target Asset ID: #<?= htmlspecialchars($animal['animal_id']); ?></small>
                </div>
            </div>
        </div>
        
        <div class="card-body p-4">
            <?php if($error): ?>
                <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4"><i class="bi bi-exclamation-triangle me-2"></i><?= $error; ?></div>
            <?php endif; ?>

            <form method="POST" id="editAnimalForm" novalidate>
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">

                <!-- Floating Animal RFID Tag -->
                <div class="form-floating mb-3">
                    <input type="text" name="animal_id" id="animalTag" class="form-control rounded-3 fw-bold text-primary" placeholder="RFID ID" value="<?= htmlspecialchars($animal['animal_id'] ?? ''); ?>" required>
                    <label for="animalTag">RFID Tag / National ID Identifier *</label>
                </div>

                <!-- Floating Owner Dropdown -->
                <div class="form-floating mb-3">
                    <select name="owner_id" id="ownerSelect" class="form-select rounded-3" required>
                        <?php foreach ($owners as $owner): ?>
                            <option value="<?= $owner['id']; ?>" <?= $animal['owner_id'] == $owner['id'] ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($owner['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <label for="ownerSelect">Assign Legal Owner / Farm Holder *</label>
                </div>
                
                <div class="row g-3 mb-3">
                    <!-- Floating Species Dropdown -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select name="species" id="speciesSelect" class="form-select rounded-3" required>
                                <option value="Bovine" <?= $animal['species'] == 'Bovine' ? 'selected' : ''; ?>>Bovine (Lo')</option>
                                <option value="Ovine" <?= $animal['species'] == 'Ovine' ? 'selected' : ''; ?>>Ovine (Ido)</option>
                                <option value="Cameline" <?= $animal['species'] == 'Cameline' ? 'selected' : ''; ?>>Cameline (Geel)</option>
                                <option value="Caprine" <?= $animal['species'] == 'Caprine' ? 'selected' : ''; ?>>Caprine (Ari)</option>
                            </select>
                            <label for="speciesSelect">Species Core Classification *</label>
                        </div>
                    </div>
                    <!-- Floating Breed -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" name="breed" id="breedInput" class="form-control rounded-3" placeholder="Breed" value="<?= htmlspecialchars($animal['breed'] ?? ''); ?>">
                            <label for="breedInput">Specific Breed Variant</label>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <!-- Floating Age -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="number" name="age_months" id="ageInput" class="form-control rounded-3" placeholder="Age" value="<?= htmlspecialchars($animal['age_months'] ?? 0); ?>" required>
                            <label for="ageInput">Age Chronology (Months) *</label>
                        </div>
                    </div>
                    <!-- Floating Gender -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select name="gender" id="genderSelect" class="form-select rounded-3" required>
                                <option value="Male" <?= $animal['gender'] == 'Male' ? 'selected' : ''; ?>>Male</option>
                                <option value="Female" <?= $animal['gender'] == 'Female' ? 'selected' : ''; ?>>Female</option>
                            </select>
                            <label for="genderSelect">Gender Spec *</label>
                        </div>
                    </div>
                </div>
                
                <!-- Floating Health Status Status -->
                <div class="form-floating mb-4">
                    <select name="status" id="statusSelect" class="form-select rounded-3" required>
                        <option value="Healthy" <?= $animal['status'] == 'Healthy' ? 'selected' : ''; ?>>Healthy</option>
                        <option value="Quarantined" <?= $animal['status'] == 'Quarantined' ? 'selected' : ''; ?>>Quarantined</option>
                        <option value="Treatment" <?= $animal['status'] == 'Treatment' ? 'selected' : ''; ?>>Under Treatment</option>
                        <option value="Exported" <?= $animal['status'] == 'Exported' ? 'selected' : ''; ?>>Exported</option>
                    </select>
                    <label for="statusSelect">Dynamic Health Inspection Status Vector *</label>
                </div>
                
                <div class="d-flex justify-content-between gap-3 border-top pt-4">
                    <a href="index.php" class="btn btn-light border rounded-pill px-4 py-2">Ka Noqo</a>
                    <button type="submit" id="saveChangesBtn" class="btn btn-success rounded-pill px-5 py-2 fw-bold shadow-sm">
                        <span id="btnTxt">Cusboonaysii Xogta</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('editAnimalForm').addEventListener('submit', function() {
        document.getElementById('saveChangesBtn').disabled = true;
        document.getElementById('btnTxt').textContent = "Initing execution transaction...";
    });
</script>
</body>
</html>