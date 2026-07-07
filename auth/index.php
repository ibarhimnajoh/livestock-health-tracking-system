<?php
// Bilaabidda Session-ka si loo kaydiyo xogta isticmaalaha.
session_start();

// Ku xidhista database-ka.
require_once '../includes/db.php';

// Kaydinta fariimaha qaladka.
$error = "";

// Hubinta in foomka lagu soo gudbiyay POST.
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Qaadashada Email-ka iyo Password-ka.
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Hubinta in meelaha muhiimka ahi aanay bannaanayn.
    if (!empty($email) && !empty($password)) {

        // Raadinta isticmaalaha iyadoo la adeegsanayo Email-ka.
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // Hubinta saxnaanta Password-ka.
        if ($user && (password_verify($password, $user['password']) || $password === $user['password'])) {

            // Kaydinta xogta isticmaalaha gudaha Session-ka.
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['fullname'] = $user['fullname'];
            $_SESSION['role'] = $user['role'];

            // U gudbinta Dashboard-ka.
            header("Location: /Livestock-Health-Export-Tracking/dashboard/dashboard.php");
            exit;

        } else {
            // Fariinta qaladka haddii Email ama Password khaldan yahay.
            $error = "Email-ka ama Password-ka waa khalad!";
        }

    } else {
        // Fariinta haddii meelaha muhiimka ahi bannaan yihiin.
        $error = "Fadlan buuxi meelaha banaan!";
    }
}
?>

<!DOCTYPE html>
<html lang="so">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VetExpert - System Sign In</title>

    <!-- Bootstrap 5 iyo Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <style>
        :root {
            --primary-green: #0f3917;
            --light-green: #2e7d32;
        }

        body {
            background-color: #F8F9FA;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-wrapper {
            background: white;
            border-radius: 0px;
            box-shadow: 0px 10px 40px rgba(0, 0, 0, 0.08);
            max-width: 1100px;
            width: 100%;
            min-height: 750px;
            border: 1px solid #e2e8f0;
            overflow: hidden;
        }

        /* Qaybta Bidix ee dabiiciga ah */
        .login-sidebar {
            position: relative;
            background-image: url('https://images.pexels.com/photos/32593438/pexels-photo-32593438.jpeg'); 
            background-size: cover;
            background-position: center;
            padding: 45px;
            color: white;
            display: flex;
            flex-column: column;
            justify-content: space-between;
            z-index: 1;
        }

        /* Green Transparency Overlay */
        .login-sidebar::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(135deg, rgba(27, 67, 36, 0.92), rgba(15, 41, 23, 0.85));
            z-index: -1;
        }

        .system-title {
            font-size: 26px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .system-subtitle {
            font-size: 11px;
            letter-spacing: 2px;
            color: #81c784;
            font-weight: 600;
        }

        .main-pitch {
            font-size: 32px;
            font-weight: 600;
            line-height: 1.3;
        }

        .pitch-desc {
            font-size: 14px;
            color: #cbd5e1;
            line-height: 1.6;
        }

        .stat-label {
            font-size: 10px;
            letter-spacing: 1px;
            color: #a7f3d0;
            text-transform: uppercase;
        }

        .stat-value {
            font-size: 28px;
            font-weight: 700;
        }

        /* Qaybta Midig ee Foomka */
        .form-section {
            padding: 50px 60px !important;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .form-label {
            font-size: 12px;
            font-weight: 600;
            color: #475569;
            margin-bottom: 6px;
        }

        .input-group-custom {
            position: relative;
        }

        .input-group-custom .form-control {
            padding-right: 40px;
            border-radius: 6px;
            border: 1px solid #cbd5e1;
            padding-top: 10px;
            padding-bottom: 10px;
            font-size: 14px;
        }

        .input-group-custom .form-control:focus {
            border-color: var(--light-green);
            box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.15);
        }

        .input-group-custom .field-icon {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            z-index: 5;
        }

        .btn-authorize {
            background-color: #0c3313;
            color: white;
            border: none;
            padding: 12px;
            font-weight: 600;
            font-size: 15px;
            border-radius: 6px;
            transition: all 0.2s ease;
        }

        .btn-authorize:hover {
            background-color: #164e20;
            color: white;
        }

        .btn-digital-card {
            background: white;
            border: 1px solid #cbd5e1;
            color: #334155;
            padding: 11px;
            font-size: 14px;
            font-weight: 500;
            border-radius: 6px;
        }

        .btn-digital-card:hover {
            background: #f8fafc;
            border-color: #94a3b8;
        }

        .divider-text {
            display: flex;
            align-items: center;
            text-align: center;
            color: #94a3b8;
            font-size: 11px;
            margin: 20px 0;
        }

        .divider-text::before, .divider-text::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #e2e8f0;
        }

        .divider-text:not(:empty)::before { margin-right: .5em; }
        .divider-text:not(:empty)::after { margin-left: .5em; }

        .notice-box {
            font-size: 10px;
            color: #64748b;
            line-height: 1.5;
            border-top: 1px solid #f1f5f9;
            padding-top: 20px;
        }
    </style>
</head>
<body>

<div class="container d-flex justify-content-center p-0">
    <div class="row login-wrapper g-0">
        
        <!-- QAYBTA BIDIX (Image overlay side) -->
        <div class="col-md-6 login-sidebar d-flex flex-column justify-content-between">
            <div>
                <div class="system-title"> VetExport</div>
                <div class="system-subtitle">Livestock Export Authority</div>
            </div>

            <div class="my-auto py-5">
                <h2 class="main-pitch mb-3">Smart Livestock Health & Export Management.</h2>
                <p class="pitch-desc">Monitor animal health, manage vaccination records, and issue secure export permits through one centralized platform.</p>
            </div>

            <div class="d-flex gap-5">
                <div>
                    <div class="stat-label">Registered Animals</div>
                    <div class="stat-value">12,540</div>
                </div>
                <div>
                    <div class="stat-label">Health Inspections</div>
                    <div class="stat-value">4,860</div>
                </div>
            </div>
        </div>

        <!-- QAYBTA MIDIG (Form side) -->
        <div class="col-md-6 form-section bg-white d-flex flex-column justify-content-between">
            
            <div class="pt-4">
                <h3 class="fw-bold text-dark mb-1" style="letter-spacing: -0.5px;">Welcome Back</h3>
                <p class="text-muted small mb-4">Enter your credentials to access the regulatory dashboard.</p>

                <!-- Soo bandhig qaladka haddii uu jiro -->
                <?php if(!empty($error)): ?>
                    <div class="alert alert-danger d-flex align-items-center gap-2 py-2 border-0 small" style="border-radius:6px;">
                        <i class="bi bi-exclamation-triangle-fill"></i> <?= $error; ?>
                    </div>
                <?php endif; ?>

                <form action="" method="POST">
                    <!-- Email Input -->
                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <div class="input-group-custom">
                            <input type="email" name="email" class="form-control" placeholder="name@authority.gov" required>
                            <i class="bi bi-envelope field-icon"></i>
                        </div>
                    </div>

                    <!-- Password Input -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label m-0">Password</label>
                        </div>
                        <div class="input-group-custom">
                            <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                            <i class="bi bi-eye-slash field-icon" style="cursor: pointer;"></i>
                        </div>
                    </div>

                    <!-- Remember workstation -->
                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" id="rememberMe">
                        <label class="form-check-label text-secondary small" for="rememberMe">
                            Remember Me
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-authorize w-100 d-flex align-items-center justify-content-center gap-2 shadow-sm">
                        Access Dashboard <i class="bi bi-box-arrow-in-right"></i>
                    </button>
                </form>

                <div class="divider-text">OR</div>

                <!-- Digital Identity Card Option 
                <button class="btn btn-digital-card w-100 d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-person-badge text-success fs-5"></i> Sign in with Digital Identity Card
                </button>
                -->

                <div class="text-center mt-4">
                    <span class="text-muted small">Need Help? </span>
                    <a href="support@vetexport.gov.so" class="text-decoration-none small fw-bold text-dark">support@vetexport.gov.so</a>
                </div>
                
                <div class="d-flex justify-content-center gap-3 mt-3" style="font-size:11px;">
                    <a href="#" class="text-secondary text-decoration-none">Version 1.0</a>
                    <span class="text-muted">•</span>
                    <a href="#" class="text-secondary text-decoration-none">© 2026 Livestock Health & Export Tracking System</a>
                </div>
            </div>

            <!-- Official Notice Footer -->
            <div class="notice-box mt-4">
                <strong>Official Notice:</strong> Authorized personnel only. All login activities are monitored and recorded.
            </div>

        </div>

    </div>
</div>

</body>
</html>