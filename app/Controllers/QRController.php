<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class QRController extends Controller {
    
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            $this->redirect("/login");
        }
    }

    /**
     * Endpoint to download the QR code as a PNG file.
     */
    public function download(): void {
        $username = $_SESSION['username'];
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
        $url = $protocol . $_SERVER['HTTP_HOST'] . '/' . $username;

        $options = [
            'version'         => \chillerlan\QRCode\Common\Version::AUTO,
            'outputInterface' => \chillerlan\QRCode\Output\QRGdImagePNG::class,
            'eccLevel'        => \chillerlan\QRCode\Common\EccLevel::L,
            'scale'           => 15, // High resolution for printing
            'outputBase64'    => false, // Return raw binary data, not base64
        ];

        $qrcode = new QRCode($options);
        $imageRaw = $qrcode->render($url);

        // Force download
        header('Content-Type: image/png');
        header('Content-Disposition: attachment; filename="algraphy_qr_' . $username . '.png"');
        header('Content-Length: ' . strlen($imageRaw));
        echo $imageRaw;
        exit;
    }
}
