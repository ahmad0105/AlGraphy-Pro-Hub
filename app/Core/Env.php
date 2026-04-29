<?php

declare(strict_types=1);

namespace App\Core;

class Env {
    public static function load(string $path): void {
        // If a .env.local file exists, use it for local development overrides
        $localPath = $path . '.local';
        $fileToLoad = file_exists($localPath) ? $localPath : $path;

        if (!file_exists($fileToLoad)) {
            return;
        }

        $lines = file($fileToLoad, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            if (strpos($line, '=') === false) {
                continue;
            }

            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);

            if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
                putenv(sprintf('%s=%s', $name, $value));
                $_ENV[$name] = $value;
                $_SERVER[$name] = $value;
            }
        }
    }
}
