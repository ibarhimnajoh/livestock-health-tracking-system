<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: ../index.php"); exit; }
require_once '../includes/db.php';

$msg = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $region = trim($_POST['region']);
    $address = trim($_POST['address']);

    if (!empty($name) && !empty($phone) && !empty($region)) {
        $stmt = $pdo->prepare("INSERT INTO owners (name, phone, email, region, address) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $phone, $email, $region, $address]);
        header("Location: index.php");
        exit;
    } else {
        $msg = "Fadlan buuxi meelaha muhiimka ah (*).";
    }
}
?>
<!DOCTYPE html>
<html lang="so">
<head>
    <meta charset="UTF-8">
    <title>Add Owner - VetExpert</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-5">

<div class="container" style="max-width: 600px;">
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <h4 class="mb-4">Register New Owner</h4>
            <?php if($msg): ?> <div class="alert alert-danger"><?= $msg; ?></div> <?php endif; ?>
            
            <form action="" method="POST">
                <div class="mb-3">
                    <label class="form-label">Full Name *</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Phone Number *</label>
                        <input type="text" name="phone" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Region *</label>
                        <select name="region" class="form-select" required>
                            <option value="Maroodi Jeex">Maroodi Jeex</option>
                            <option value="Bari">Bari</option>
                            <option value="Nugaal">Nugaal</option>
                            <option value="Togdheer">Togdheer</option>
                            <option value="Awdal">Awdal</option>
                            <option value="Sanaag">Sanaag</option>
                            <option value="Sool">Sool</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control">
                </div>
                <div class="mb-4">
                    <label class="form-label">Address / Farm Location</label>
                    <textarea name="address" class="form-control" rows="2"></textarea>
                </div>
                <div class="d-flex justify-content-between">
                    <a href="index.php" class="btn btn-light">Cancel</a>
                    <button type="submit" class="btn btn-success">Save Owner</button>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>