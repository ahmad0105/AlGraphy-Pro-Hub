<?php

use App\Core\Router;
use App\Controllers\AuthController;
use App\Controllers\UserController;
use App\Controllers\DashboardController;
use App\Controllers\LinkController;
use App\Controllers\ProfileController;
use App\Controllers\RedirectController;

$router = new Router();

// Auth Routes
$router->add('/', fn() => (new AuthController())->login());
$router->add('/login', fn() => (new AuthController())->login());
$router->add('/register', fn() => (new AuthController())->register());
$router->add('/logout', fn() => (new AuthController())->logout());
$router->add('/resend-code', fn() => (new AuthController())->resend());

// Password Reset Routes
$router->add('/forgot-password', fn() => (new AuthController())->forgotPassword());
$router->add('/reset-password', fn() => (new AuthController())->resetPassword());

// OTP Routes
$router->add('/verify-otp', fn() => (new AuthController())->verify());

// Dashboard Routes
$router->add('/dashboard', fn() => (new DashboardController())->index());
$router->add('/settings', fn() => (new UserController())->settings());
$router->add('/user/update-social', fn() => (new UserController())->updateSocial());
$router->add('/user/reorder-socials', fn() => (new UserController())->reorderSocials());

// Link Builder Routes
$router->add('/link/add', fn() => (new LinkController())->add());
$router->add('/link/delete', fn() => (new LinkController())->delete());
$router->add('/link/update', fn() => (new LinkController())->update());
$router->add('/link/reorder', fn() => (new LinkController())->updateOrder());
$router->add('/link/toggle', fn() => (new LinkController())->toggle());

// QR Code Routes
$router->add('/qr/download', fn() => (new \App\Controllers\QRController())->download());

// Analytics Tracking Route
$router->add('/l', function() {
    $id = $_GET['id'] ?? null;
    if ($id) {
        (new RedirectController())->track($id);
    } else {
        header("Location: /404");
    }
});

return $router;