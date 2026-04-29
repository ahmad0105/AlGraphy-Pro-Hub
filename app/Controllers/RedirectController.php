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

        // 3. Smart Redirect: Detect if it's a phone number or email
        $url = trim($link['url']);
        
        // If it starts with + or is just numbers (phone detection)
        if (preg_match('/^\+?[0-9\s\-]{7,20}$/', $url)) {
            $cleanPhone = str_replace([' ', '-', '(', ')'], '', $url);
            $url = 'tel:' . $cleanPhone;
        } 
        // If it's an email
        elseif (filter_var($url, FILTER_VALIDATE_EMAIL)) {
            $url = 'mailto:' . $url;
        }
        // Ensure standard URLs have protocol
        elseif (!str_starts_with($url, 'http') && !str_starts_with($url, 'tel:') && !str_starts_with($url, 'mailto:')) {
            $url = 'https://' . $url;
        }

        header("Location: " . $url);
        exit();
    }
}
