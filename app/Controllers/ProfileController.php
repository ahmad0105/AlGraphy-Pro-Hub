<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

class ProfileController extends Controller {
    /**
     * Show the public profile page
     */
    public function show(string $username): void {
        // Using magic __get to access userModel
        $user = $this->userModel->findByUsername($username);

        if (!$user) {
            header("HTTP/1.0 404 Not Found");
            $this->view('errors/404');
            return;
        }

        // Fetch user links - Active only for public profile
        $links = $this->linkModel->getLinksByUserId((int)$user['id'], true);

        // --- Analytics: Log Page View ---
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        
        // Simple session-based check to avoid logging refresh spam as multiple views in short time
        $sessionKey = 'viewed_profile_' . $user['id'];
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        if (!isset($_SESSION[$sessionKey])) {
            $this->analyticsModel->logView((int)$user['id'], $ip, $userAgent);
            $_SESSION[$sessionKey] = time();
        }

        $this->view('profile/show', [
            'user' => $user,
            'links' => $links
        ]);
    }
}
