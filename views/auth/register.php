<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - AlGraphy Pro Hub</title>
    <link rel="stylesheet" href="<?php echo \App\Core\Config::asset('css/style.css'); ?>">
</head>

<body>
    <div class="auth-container">
        <div class="logo">
            <h1>AlGraphy <span>Pro Hub</span></h1>
        </div>
        <div class="card">
            <h2>Claim Your Link</h2>

            <!-- Display session errors & if username is already taken -->
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($_SESSION['error']);
                unset($_SESSION['error']); ?></div>
            <?php endif; ?>

            <!-- Registration Form: Data is sent to the 'register' route via POST -->
            <form action="<?php echo \App\Core\Config::url('register'); ?>" method="POST">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required placeholder="yourname">
                </div>
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required placeholder="name@example.com">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required placeholder="••••••••">
                </div>
                <button type="submit" class="btn">Create Account</button>
            </form>

            <div class="auth-links">
                <p>Already have an account? <a href="<?php echo \App\Core\Config::url('login'); ?>">Sign In</a></p>
            </div>
        </div>
    </div>
    <script src="<?php echo \App\Core\Config::asset('js/main.js'); ?>"></script>
</body>

</html>