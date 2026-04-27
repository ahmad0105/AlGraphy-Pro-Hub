<?php

declare(strict_types=1);

namespace App\Core;

class Config {
    /**
     * Dynamically detect the base URL
     */
    public static function getBaseUrl(): string {
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $publicPos = strpos($scriptName, '/public/index.php');
        if ($publicPos !== false) {
            return substr($scriptName, 0, $publicPos);
        }
        return str_replace('/public', '', dirname($scriptName));
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
        return self::getBaseUrl() . '/public/assets/' . ltrim($path, '/');
    }
}
