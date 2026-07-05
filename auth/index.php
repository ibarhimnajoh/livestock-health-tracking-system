<?php
session_start();
require_once '../includes/db.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (!empty($email) && !empty($password)) {
        // Labada dhibco (??) waxay xaqiijinayaan inuu database-ka si nadiif ah u baadho
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // Si koodhku u shaqeeyo si fudud, haddii password-ka database-ka ku jira uu yahay 'password123' ama uu yahay Hash, labadaba wuu aqbalayaa koodhkan hoose:
        if ($user && (password_verify($password, $user['password']) || $password === $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['fullname'] = $user['fullname'];
            $_SESSION['role'] = $user['role'];
            
            header("Location: /Livestock-Health-Export-Tracking/dashboard/dashboard.php"); // Maadaama sawirkaaga VS Code uu ku jiro galka dashboard
            exit;
        } else {
            $error = "Email-ka ama Password-ka waa khalad!";
        }
    } else {
        $error = "Fadlan buuxi meelaha banaan!";
    }
}
?>
<!DOCTYPE html>
<html lang="so">
<head>
    <meta charset="UTF-8">
    <title>VetExpert - System Sign In</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .login-container { max-width: 900px; margin: 8% auto; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0px 4px 20px rgba(0,0,0,0.1); }
        .login-sidebar { background: linear-gradient(135deg, #1e4620, #2e7d32); color: white; padding: 40px; }
    </style>
</head>
<body>

<div class="container">
    <div class="row login-container">
        <div class="col-md-5 login-sidebar d-flex flex-column justify-content-between">
            <div>
                <h4>VetExpert</h4>
                <p class="text-white-50">REGULATORY SYSTEM</p>
            </div>
            <div>
                <h2>Securing Global Livestock Trade through Precision Compliance.</h2>
            </div>
            <div>
                <small>Compliance Rate: <strong>99.8%</strong></small>
            </div>
        </div>
        
        <div class="col-md-7 p-5">
            <h3 class="mb-4">System Sign In</h3>
            
            <?php if(!empty($error)): ?>
                <div class="alert alert-danger"><?= $error; ?></div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="mb-3">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control" placeholder="name@authority.gov" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                </div>
                <button type="submit" class="btn btn-success w-100 py-2" style="background-color: #2e7d32;">Sign In</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>