<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

class RedirectController extends Controller {
    /**
     * Track click and redirect
     */
    public function track(string $linkId): void {
        // Use linkModel via magic __get
        $link = $this->linkModel->findById((int)$linkId);
        
        if (!$link) {
            header("Location: /404");
            exit();
        }

        // 2. Log the click
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        
        $this->analyticsModel->logClick((int)$linkId, $ip, $userAgent);

        // 3. Redirect to the actual destination
        header("Location: " . $link['url']);
        exit();
    }
}
