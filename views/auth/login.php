<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - AlGraphy Pro Hub</title>
    <!-- Use Config helper to load the main CSS file -->
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
                <img src="<?php echo \App\Core\Config::asset('logo/Red_logo_algraphy.svg'); ?>" alt="AlGraphy" class="navbar-logo-img" fetchpriority="high" loading="eager">
                <h1 class="navbar-logo-text">Hub</h1>
            </a>
        </div>
        <div class="card">
            <h2>Welcome Back</h2>
            
            <!-- Show session messages as toasts -->
            <?php if (isset($_SESSION['success'])): ?>
                <script>window.addEventListener('DOMContentLoaded', () => showToast("<?php echo $_SESSION['success']; ?>", 'success'));</script>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <script>window.addEventListener('DOMContentLoaded', () => showToast("<?php echo $_SESSION['error']; ?>", 'error'));</script>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <!-- Login Form: Submits credentials to the 'login' route via POST -->
            <form action="<?php echo \App\Core\Config::url('login'); ?>" method="POST">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required placeholder="name@example.com">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required placeholder="••••••••">
                </div>
                <button type="submit" class="btn">Sign In</button>
            </form>

            <div class="auth-links">
                <!-- Links for password recovery and new user registration -->
                <p><a href="<?php echo \App\Core\Config::url('forgot-password'); ?>">Forgot password?</a></p>
                <p>Don't have an account? <a href="<?php echo \App\Core\Config::url('register'); ?>">Sign Up</a></p>
            </div>
        </div>
    </div>
    <!-- Include main JS for animations and dynamic features -->
    <script src="<?php echo \App\Core\Config::asset('js/toast.js'); ?>"></script>
    <script src="<?php echo \App\Core\Config::asset('js/main.js'); ?>"></script>
</body>

</html>