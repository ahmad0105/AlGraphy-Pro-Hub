<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - AlGraphy Pro Hub</title>
    <!-- Link to the main CSS file using the Config helper -->
    <link rel="stylesheet" href="<?php echo \App\Core\Config::asset('css/style.css'); ?>">
    <link rel="stylesheet" href="<?php echo \App\Core\Config::asset('css/toast.css'); ?>">
    <!-- Favicons -->
    <link rel="icon" type="image/png" href="<?php echo \App\Core\Config::asset('logo/favicon-96x96.png'); ?>" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="<?php echo \App\Core\Config::asset('logo/favicon.svg'); ?>" />
    <link rel="shortcut icon" href="<?php echo \App\Core\Config::asset('logo/favicon.svg'); ?>" />
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo \App\Core\Config::asset('logo/apple-touch-icon.png'); ?>" />
    <link rel="manifest" href="<?php echo \App\Core\Config::asset('logo/site.webmanifest'); ?>" crossorigin="use-credentials" />
</head>
<body>
    <div class="auth-container">
        <div class="logo">
            <a href="<?php echo \App\Core\Config::url('dashboard'); ?>" class="navbar-logo-link">
                <img src="<?php echo \App\Core\Config::asset('logo/Red_logo_algraphy.svg'); ?>" alt="AlGraphy" class="navbar-logo-img">
                <h1 class="navbar-logo-text">Hub</h1>
            </a>
        </div>
        <div class="card">
            <h2>Reset Password</h2>
            <p style="text-align: center; color: var(--text-muted); margin-bottom: 20px; font-size: 0.9rem;">
                Enter your email and we'll send you a link to reset your password.
            </p>
            
            <!-- Show session messages as toasts -->
            <?php if (isset($_SESSION['success'])): ?>
                <script>window.addEventListener('DOMContentLoaded', () => showToast("<?php echo $_SESSION['success']; ?>", 'success'));</script>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <script>window.addEventListener('DOMContentLoaded', () => showToast("<?php echo $_SESSION['error']; ?>", 'error'));</script>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <!-- Forgot Password Form: Submits the user's email to start the reset process -->
            <form action="<?php echo \App\Core\Config::url('forgot-password'); ?>" method="POST">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required placeholder="name@example.com">
                </div>
                <button type="submit" class="btn">Send Reset Link</button>
            </form>

            <div class="auth-links">
                <!-- Link to return to the login page -->
                <p>Remembered your password? <a href="<?php echo \App\Core\Config::url('login'); ?>">Back to Login</a></p>
            </div>
        </div>
    </div>
    <!-- Include the main JS file for alert animations and global logic -->
    <script src="<?php echo \App\Core\Config::asset('js/toast.js'); ?>"></script>
    <script src="<?php echo \App\Core\Config::asset('js/main.js'); ?>"></script>
</body>
</html>
