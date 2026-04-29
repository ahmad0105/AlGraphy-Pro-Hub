<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email - AlGraphy Pro Hub</title>
    <link rel="stylesheet" href="<?php echo \App\Core\Config::asset('css/style.css'); ?>">
    <link rel="stylesheet" href="<?php echo \App\Core\Config::asset('css/toast.css'); ?>">
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
            <h2>Verify Your Email</h2>
            <p style="text-align: center; color: var(--text-muted); margin-bottom: 20px; font-size: 0.9rem;">
                We've sent a 6-digit code to your email. It will expire in 15 minutes.
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

            <!-- Verification form: user enters the 6-digit OTP code -->
            <form action="<?php echo \App\Core\Config::url('verify'); ?>" method="POST">
                <div class="form-group">
                    <label for="otp">Enter 6-Digit Code</label>
                    <input type="text" id="otp" name="otp" required placeholder="000000" maxlength="6" pattern="\d{6}" style="text-align: center; letter-spacing: 10px; font-size: 1.5rem;">
                </div>
                <button type="submit" class="btn">Verify Account</button>
            </form>

            <div class="auth-links">
                <!-- Link to trigger OTP resend logic -->
                <p>Didn't get the code? <a href="<?php echo \App\Core\Config::url('resend-code'); ?>">Resend Code</a></p>
                <!-- Link to go back to registration if the user entered the wrong email -->
                <p style="margin-top: 10px;"><a href="<?php echo \App\Core\Config::url('register'); ?>" style="color: var(--text-muted); font-size: 0.8rem;">Try another email</a></p>
            </div>
        </div>
    </div>
    <script src="<?php echo \App\Core\Config::asset('js/toast.js'); ?>"></script>
    <script src="<?php echo \App\Core\Config::asset('js/main.js'); ?>"></script>
</body>
</html>
