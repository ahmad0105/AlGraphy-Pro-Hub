<?php

declare(strict_types=1);

namespace App\Core;

use App\Core\Config;
use AllowDynamicProperties;

#[AllowDynamicProperties]
abstract class Controller {
    /**
     * Render a view file
     */
    public function view(string $path, array $data = []): void {
        extract($data);
        $viewPath = __DIR__ . "/../../views/" . $path . ".php";
        
        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            die("View $path not found.");
        }
    }

    /**
     * Redirect to a URL
     */
    protected function redirect(string $url): void {
        $fullUrl = Config::url($url);
        header("Location: $fullUrl");
        exit();
    }

    /**
     * Sanitize input data
     * Strips HTML tags and trims whitespace to prevent stored XSS.
     * Output escaping (htmlspecialchars) must still be applied in Views.
     */
    protected function sanitize(string $data): string {
        return trim(strip_tags($data));
    }

    /**
     * Validate and sanitize a CSS color value (hex only)
     * Prevents CSS injection through user-controlled style values
     */
    protected function sanitizeColor(string $color): string {
        // Only allow valid hex colors like #000, #000000, #00000080
        if (preg_match('/^#([A-Fa-f0-9]{3,8})$/', trim($color))) {
            return trim($color);
        }
        return '#000000'; // Safe fallback
    }

    /**
     * Validate a CSS dimension value (e.g. button_radius)
     * Only allows safe patterns like "12px", "50px", "0px"
     */
    protected function sanitizeCSSValue(string $value): string {
        if (preg_match('/^\d{1,3}(px|rem|em|%)$/', trim($value))) {
            return trim($value);
        }
        return '12px'; // Safe fallback
    }

    /**
     * Smartly detect the platform icon based on the URL
     */
    protected function getPlatformIcon(string $url): string {
        $url = strtolower($url);
        $icons = [
            'instagram.com' => 'fab fa-instagram',
            'twitter.com'   => 'fab fa-x-twitter',
            'x.com'         => 'fab fa-x-twitter',
            'tiktok.com'    => 'fab fa-tiktok',
            'youtube.com'   => 'fab fa-youtube',
            'youtu.be'      => 'fab fa-youtube',
            'facebook.com'  => 'fab fa-facebook-f',
            'fb.com'        => 'fab fa-facebook-f',
            'whatsapp.com'  => 'fab fa-whatsapp',
            'wa.me'         => 'fab fa-whatsapp',
            'snapchat.com'  => 'fab fa-snapchat',
            'linkedin.com'  => 'fab fa-linkedin-in',
            'threads.net'   => 'fab fa-threads',
            'github.com'    => 'fab fa-github',
            'twitch.tv'     => 'fab fa-twitch',
            'spotify.com'   => 'fab fa-spotify',
            'apple.com'     => 'fab fa-apple',
            'amazon.com'    => 'fab fa-amazon',
            'pinterest.com' => 'fab fa-pinterest',
            'reddit.com'    => 'fab fa-reddit',
            'discord.gg'    => 'fab fa-discord',
            'telegram.me'   => 'fab fa-telegram',
            't.me'          => 'fab fa-telegram',
        ];

        foreach ($icons as $domain => $icon) {
            if (str_contains($url, $domain)) {
                return $icon;
            }
        }

        return 'fas fa-link'; // Default icon
    }

    /**
     * Set session flash message
     */
    protected function setFlash(string $key, string $message): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION[$key] = $message;
    }

    /**
     * Magic Method __get
     * Allows dynamic loading of models
     */
    public function __get(string $name) {
        if (str_ends_with($name, 'Model')) {
            $modelName = "App\\Models\\" . ucfirst(str_replace('Model', '', $name));
            if (class_exists($modelName)) {
                $this->$name = new $modelName();
                return $this->$name;
            }
        }
        return null;
    }
}
