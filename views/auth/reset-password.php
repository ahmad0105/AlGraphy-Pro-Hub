



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - AlGraphy Pro Hub</title>
    <link rel="stylesheet" href="<?php echo \App\Core\Config::asset('css/style.css'); ?>">
</head>

<!--
Reset Password View
This view is displayed 
http://localhost/algraphyHub/reset-password?token=(thetoken)
 -->

<body>
    <div class="auth-container">
        <div class="logo">
            <h1>AlGraphy <span>Pro Hub</span></h1>
        </div>
        <div class="card">
            <h2>Reset Password</h2>

            <!-- Display error messages (if the reset link has expired) -->
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-error">
                    <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
                </div>
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
    <script src="<?php echo \App\Core\Config::asset('js/main.js'); ?>"></script>
</body>

</html>