<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: ../index.php"); exit; }
require_once '../includes/db.php';

// Soo qaad dhamaan owners si loogu xusho dropdown-ka
$owners = $pdo->query("SELECT id, name FROM owners")->fetchAll();

$msg = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $animal_id = "ANM-" . rand(1000, 9999) . "-" . strtoupper(substr(md5(uniqid()), 0, 2)); // Auto generated RFID tag
    $owner_id = $_POST['owner_id'];
    $species = $_POST['species'];
    $breed = trim($_POST['breed']);
    $age_months = intval($_POST['age_months']);
    $gender = $_POST['gender'];

    if (!empty($owner_id) && !empty($species)) {
        $stmt = $pdo->prepare("INSERT INTO animals (animal_id, owner_id, species, breed, age_months, gender) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$animal_id, $owner_id, $species, $breed, $age_months, $gender]);
        header("Location: index.php");
        exit;
    } else {
        $msg = "Fadlan buuxi meelaha loo baahan yahay.";
    }
}
?>
<!DOCTYPE html>
<html lang="so">
<head>
    <meta charset="UTF-8">
    <title>Register Animal - VetExpert</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-5">

<div class="container" style="max-width: 650px;">
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <h4 class="mb-4">Register New Animal Asset</h4>
            <?php if($msg): ?> <div class="alert alert-danger"><?= $msg; ?></div> <?php endif; ?>

            <form action="" method="POST">
                <div class="mb-3">
                    <label class="form-label">Select Owner *</label>
                    <select name="owner_id" class="form-select" required>
                        <option value="">-- Dooro Milkiilaha --</option>
                        <?php foreach($owners as $owner): ?>
                            <option value="<?= $owner['id']; ?>"><?= htmlspecialchars($owner['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Species *</label>
                        <select name="species" class="form-select" required>
                            <option value="Bovine (Cow)">Bovine (Cow)</option>
                            <option value="Ovine (Sheep)">Ovine (Sheep)</option>
                            <option value="Caprine (Goat)">Caprine (Goat)</option>
                            <option value="Cameline (Camel)">Cameline (Camel)</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Breed (Nooca)</label>
                        <input type="text" name="breed" class="form-control" placeholder="Tusaale: Barka, Somali Sheep">
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Age (Months)</label>
                        <input type="number" name="age_months" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Gender *</label>
                        <div class="mt-2">
                            <input type="radio" name="gender" value="Male" id="m" checked> <label for="m" class="me-3">Male</label>
                            <input type="radio" name="gender" value="Female" id="f"> <label for="f">Female</label>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="index.php" class="btn btn-light">Back</a>
                    <button type="submit" class="btn btn-success">Register Asset</button>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>