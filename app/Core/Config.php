<?php

declare(strict_types=1);

namespace App\Core;

class Config {
    /**
     * Dynamically detect the base URL
     */
    public static function getBaseUrl(): string {
        // Use APP_URL from .env if defined
        if (!empty($_ENV['APP_URL'])) {
            $url = rtrim($_ENV['APP_URL'], '/');
            // Convert to protocol-relative URL (e.g., //domain.com) 
            // This is the most compatible way for Safari/HTTPS issues
            $url = str_replace(['http://', 'https://'], '//', $url);
            return $url;
        }

        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        // If we are in public/index.php (proxied), the base is the directory containing public/
        $publicPos = strpos($scriptName, '/public/index.php');
        if ($publicPos !== false) {
            return rtrim(substr($scriptName, 0, $publicPos), '/');
        }
        return rtrim(str_replace('/public', '', dirname($scriptName)), '/');
    }

    /**
     * Helper to generate full URLs
     */
    public static function url(string $path = ''): string {
        return self::getBaseUrl() . '/' . ltrim($path, '/');
    }

    /**
     * Helper to generate asset URLs
     */
    public static function asset(string $path = ''): string {
        // With our new .htaccess, we don't need 'public/' in the URL
        return self::getBaseUrl() . '/assets/' . ltrim($path, '/');
    }

    /**
     * Helper to generate upload URLs
     */
    public static function upload(string $path = ''): string {
        return self::getBaseUrl() . '/uploads/' . ltrim($path, '/');
    }
    /**
     * Helper to get absolute filesystem path to the root directory
     */
    public static function root(string $path = ''): string {
        return dirname(__DIR__, 2) . '/' . ltrim($path, '/');
    }
    
}
