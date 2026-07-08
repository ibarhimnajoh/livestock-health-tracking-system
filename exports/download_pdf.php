<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: ../index.php"); exit; }
require_once '../includes/db.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$query = "SELECT p.*, a.animal_id as rfid, a.species, a.breed, o.name as owner_name, o.region, u.fullname as officer_name 
          FROM export_permits p 
          JOIN animals a ON p.animal_id = a.id 
          JOIN owners o ON a.owner_id = o.id 
          JOIN users u ON p.officer_id = u.id 
          WHERE p.id = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$id]);
$permit = $stmt->fetch();

if (!$permit) { die("Ruqsaddan lama helin."); }
?>
<!DOCTYPE html>
<html lang="so">
<head>
    <meta charset="UTF-8">
    <title>Permit - <?= $permit['permit_number']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #fff; font-family: 'Courier New', Courier, monospace; }
        .permit-box { border: 4px double #000; padding: 30px; margin-top: 20px; }
        .header-title { border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        @media print {
            .no-print { display: none; }
            body { padding: 0; }
            .permit-box { border: none; }
        }
    </style>
</head>
<body>

<div class="container my-4" style="max-width: 800px;">
    <div class="no-print text-end mb-3">
        <button onclick="window.print();" class="btn btn-dark">Daabac / Download PDF</button>
        <a href="index.php" class="btn btn-secondary">Ku noqo Liiska</a>
    </div>

    <div class="permit-box">
        <div class="text-center header-title">
            <h3>LIVESTOCK EXPORT AUTHORITY</h3>
            <h5>OFFICIAL SANITARY EXPORT PERMIT</h5>
            <small>System Verification ID: <?= $permit['permit_number']; ?></small>
        </div>

        <div class="row my-4">
            <div class="col-6">
                <h6><strong>EXPORTER / OWNER DETAILS:</strong></h6>
                <p class="mb-1">Name: <?= htmlspecialchars($permit['owner_name']); ?></p>
                <p class="mb-1">Origin Region: <?= htmlspecialchars($permit['region']); ?></p>
            </div>
            <div class="col-6 text-end">
                <h6><strong>DESTINATION & LOGISTICS:</strong></h6>
                <p class="mb-1">Destination Country: <strong><?= htmlspecialchars($permit['destination_country']); ?></strong></p>
                <p class="mb-1">Issue Date: <?= $permit['issue_date']; ?></p>
            </div>
        </div>

        <hr>

        <h6><strong>ANIMAL ASSET DESCRIPTION:</strong></h6>
        <table class="table table-bordered mt-2">
            <thead>
                <tr>
                    <th>RFID Tag Number</th>
                    <th>Species Group</th>
                    <th>Breed Description</th>
                    <th>Health Status</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>#<?= htmlspecialchars($permit['rfid']); ?></strong></td>
                    <td><?= htmlspecialchars($permit['species']); ?></td>
                    <td><?= htmlspecialchars($permit['breed']); ?></td>
                    <td><span class="text-success">✔ COMPLIANT / APPROVED</span></td>
                </tr>
            </tbody>
        </table>

        <div class="mt-5 pt-4 row">
            <div class="col-6">
                <small>Authorized by:</small>
                <p class="fw-bold border-top pt-2 mt-4"><?= htmlspecialchars($permit['officer_name']); ?><br><small class="text-muted">Export Control Officer</small></p>
            </div>
            <div class="col-6 text-end">
                <small>Official Seal Stamp</small>
                <div class="mt-3"><i class="text-muted">[ VETEXPERT DIGITAL VERIFIED ]</i></div>
            </div>
        </div>
    </div>
</div>

</body>
</html>