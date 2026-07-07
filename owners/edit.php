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

// 2. Soo qaad xogta owner-ka dhabta ah ee database-ka ku jirta
$stmt = $pdo->prepare("SELECT * FROM owners WHERE id = ?");
$stmt->execute([$id]);
$owner = $stmt->fetch();

if (!$owner) { 
    die("Owner-kan laguma helin database-ka."); 
}

// 3. Marka badhanka Save Changes la gujiyo (POST Request)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $email = $_POST['email'] ?? '';
    $region = $_POST['region'] ?? '';
    $address = $_POST['address'] ?? '';

    if (!empty($name) && !empty($phone) && !empty($region)) {
        // Waxaan u cusboonaysiinaynaa si waafaqsan khaanadaha database-kaaga dhabta ah
        $update = $pdo->prepare("UPDATE owners SET name = ?, phone = ?, email = ?, region = ?, address = ? WHERE id = ?");
        $update->execute([$name, $phone, $email, $region, $address, $id]);
        
        header("Location: index.php?success=Updated");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="so">
<head>
    <meta charset="UTF-8">
    <title>Edit Owner - VetExpert</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-5">

    <div class="container" style="max-width: 550px;">
        <div class="card shadow-sm border-0 p-4">
            <h4 class="mb-3 text-dark fw-bold">Edit Owner Information</h4>
            <hr class="mb-4">
            
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Full Name</label>
                    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($owner['name'] ?? ''); ?>" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-semibold">Phone Number</label>
                    <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($owner['phone'] ?? ''); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Email Address</label>
                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($owner['email'] ?? ''); ?>">
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-semibold">Region</label>
                    <select name="region" class="form-select" required>
                        <option value="Maroodi Jeex" <?= ($owner['region'] ?? '') == 'Maroodi Jeex' ? 'selected' : ''; ?>>Maroodi Jeex</option>
                        <option value="Saaxil" <?= ($owner['region'] ?? '') == 'Saaxil' ? 'selected' : ''; ?>>Saaxil</option>
                        <option value="Togdheer" <?= ($owner['region'] ?? '') == 'Togdheer' ? 'selected' : ''; ?>>Togdheer</option>
                        <option value="Awdal" <?= ($owner['region'] ?? '') == 'Awdal' ? 'selected' : ''; ?>>Awdal</option>
                        <option value="Sanaag" <?= ($owner['region'] ?? '') == 'Sanaag' ? 'selected' : ''; ?>>Sanaag</option>
                        <option value="Sool" <?= ($owner['region'] ?? '') == 'Sool' ? 'selected' : ''; ?>>Sool</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Physical Address</label>
                    <textarea name="address" class="form-control" rows="2"><?= htmlspecialchars($owner['address'] ?? ''); ?></textarea>
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