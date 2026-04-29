



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - AlGraphy Pro Hub</title>
    <link rel="stylesheet" href="<?php echo \App\Core\Config::asset('css/style.css'); ?>">
    <link rel="stylesheet" href="<?php echo \App\Core\Config::asset('css/toast.css'); ?>">
    <!-- Favicons -->
    <link rel="icon" type="image/png" href="<?php echo \App\Core\Config::asset('logo/favicon-96x96.png'); ?>" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="<?php echo \App\Core\Config::asset('logo/favicon.svg'); ?>" />
    <link rel="shortcut icon" href="<?php echo \App\Core\Config::asset('logo/favicon.svg'); ?>" />
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo \App\Core\Config::asset('logo/apple-touch-icon.png'); ?>" />
    <link rel="manifest" href="<?php echo \App\Core\Config::asset('logo/site.webmanifest'); ?>" crossorigin="use-credentials" />
</head>

<!--
Reset Password View
This view is displayed 
http://localhost/algraphyHub/reset-password?token=(thetoken)
 -->

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

            <!-- Show session errors as toasts -->
            <?php if (isset($_SESSION['error'])): ?>
                <script>window.addEventListener('DOMContentLoaded', () => showToast("<?php echo $_SESSION['error']; ?>", 'error'));</script>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <!-- Password Reset Form: Submits to the reset-password route via POST -->
            <form action="<?php echo \App\Core\Config::url('reset-password'); ?>" method="POST">
                <!-- IMPORTANT: Hidden field to pass the reset token back to the server -->
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                
                <div class="form-group">
                    <label for="password">New Password</label>
                    <input type="password" id="password" name="password" required placeholder="••••••••">
                </div>
                <button type="submit" class="btn">Update Password</button>
            </form>
        </div>
    </div>
    <script src="<?php echo \App\Core\Config::asset('js/toast.js'); ?>"></script>
    <script src="<?php echo \App\Core\Config::asset('js/main.js'); ?>"></script>
</body>

</html>