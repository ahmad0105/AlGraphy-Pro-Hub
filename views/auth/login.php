<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - AlGraphy Pro Hub</title>
    <!-- Use Config helper to load the main CSS file -->
    <link rel="stylesheet" href="<?php echo \App\Core\Config::asset('css/style.css'); ?>">
</head>

<body>
    <div class="auth-container">
        <div class="logo">
            <h1>AlGraphy <span>Pro Hub</span></h1>
        </div>
        <div class="card">
            <h2>Welcome Back</h2>
            
            <!-- Show session success messages (after password reset or verification) -->
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($_SESSION['success']); 
                unset($_SESSION['success']); ?></div>
            <?php endif; ?>

            <!-- Show session errors (invalid login credentials) -->
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
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
    <script src="<?php echo \App\Core\Config::asset('js/main.js'); ?>"></script>
</body>

</html>