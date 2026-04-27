<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class DashboardController extends Controller {

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            $this->redirect("/login");
        }
    }

    public function index(): void {
        $userId = (int)$_SESSION['user_id'];
        $user = $this->userModel->findById($userId);

        // Security: Check if user is verified
        if (!$user['is_verified']) {
            $this->redirect("/verify-otp");
        }

        // Fetch user links using professional pattern
        $links = $this->linkModel->getLinksByUserId($userId);

        // Fetch Real Statistics
        $stats = $this->analyticsModel->getUserStats($userId);

        // Generate QR Code Base64
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
        $url = $protocol . $_SERVER['HTTP_HOST'] . '/' . $user['username'];
        $options = [
            'version'         => \chillerlan\QRCode\Common\Version::AUTO,
            'outputInterface' => \chillerlan\QRCode\Output\QRGdImagePNG::class,
            'eccLevel'        => \chillerlan\QRCode\Common\EccLevel::L,
            'scale'           => 5,
            'outputBase64'    => true,
        ];
        $qrcode = (new QRCode($options))->render($url);

        $this->view("dashboard/index", [
            'user' => $user,
            'links' => $links,
            'stats' => $stats,
            'qrcode' => $qrcode
        ]);
    }
}

