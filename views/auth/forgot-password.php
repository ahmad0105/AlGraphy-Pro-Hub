<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - AlGraphy Pro Hub</title>
    <!-- Link to the main CSS file using the Config helper -->
    <link rel="stylesheet" href="<?php echo \App\Core\Config::asset('css/style.css'); ?>">
</head>
<body>
    <div class="auth-container">
        <div class="logo">
            <h1>AlGraphy <span>Pro Hub</span></h1>
        </div>
        <div class="card">
            <h2>Reset Password</h2>
            <p style="text-align: center; color: var(--text-muted); margin-bottom: 20px; font-size: 0.9rem;">
                Enter your email and we'll send you a link to reset your password.
            </p>
            
            <!-- Show success message if the reset link was sent successfully -->
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
            <?php endif; ?>

            <!-- Show error message if the email was not found or something went wrong -->
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
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
    <script src="<?php echo \App\Core\Config::asset('js/main.js'); ?>"></script>
</body>
</html>
