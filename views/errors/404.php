<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - User Not Found</title>
    <link rel="stylesheet" href="<?php echo \App\Core\Config::asset('css/style.css'); ?>">
    <link rel="stylesheet" href="<?php echo \App\Core\Config::asset('css/404.css'); ?>">

</head>

<body>
    <h1>404</h1>
    <p>User Not Found</p>
    <p style="font-size: 0.9rem;">The profile you are looking for does not exist.</p>

    <!--if user is logged in redirect to dashboard else redirect to login-->
    <?php if (isset($_SESSION['user_id'])): ?>
        <a href="<?php echo \App\Core\Config::url('dashboard'); ?>" class="btn-back">Go Back to Dashboard</a>
    <?php endif; ?>

</body>

</html>