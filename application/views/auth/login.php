<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Net Global Solutions Inc - Ticketing Portal</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0A1F44, #1E3A8A);
        }

        .login-container {
            width: 100%;
            max-width: 430px;
            padding: 20px;
        }

        .login-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 40px 35px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.25);
        }

        .company-name {
            font-weight: 600;
            font-size: 1.3rem;
            color: #0A1F44;
        }

        .portal-label {
            font-size: 0.85rem;
            color: #6b7280;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-control {
            width: 100%;
            padding: 14px;
            border-radius: 10px;
            border: 1px solid #d1d5db;
            font-size: 0.9rem;
            transition: 0.2s ease;
        }

        .form-control:focus {
            border-color: #2563EB;
            outline: none;
            box-shadow: 0 0 0 2px rgba(37,99,235,0.15);
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            border-radius: 10px;
            border: none;
            background: #0A1F44;
            color: white;
            font-weight: 500;
            cursor: pointer;
            transition: 0.3s ease;
        }

        .btn-login:hover {
            background: #2563EB;
        }

        .alert {
            background: #fee2e2;
            color: #991b1b;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 15px;
            font-size: 0.85rem;
        }

        .footer-note {
            text-align: center;
            margin-top: 25px;
            font-size: 0.75rem;
            color: #9ca3af;
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="login-card">

        <div class="company-name">Net Global Solutions Inc</div>
        <div class="portal-label">Internal Ticketing Portal</div>

        <?php if($this->session->flashdata('error')): ?>
            <div class="alert">
                <?= $this->session->flashdata('error'); ?>
            </div>
        <?php endif; ?>

        <form method="post" action="<?= site_url('auth/do_login') ?>">
            <div class="form-group">
                <input type="text" name="username" class="form-control" placeholder="Username" required>
            </div>

            <div class="form-group">
                <input type="password" name="password" class="form-control" placeholder="Password" required>
            </div>

            <button class="btn-login">Sign In</button>
        </form>

        <div class="footer-note">
            © <?= date('Y') ?> Net Global Solutions Inc
        </div>

    </div>
</div>

</body>
</html>