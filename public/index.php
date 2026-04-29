<?php
declare(strict_types=1);

// Initialize Buffering & Errors
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', '0');
date_default_timezone_set('Asia/Riyadh');

// Load Composer Autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables
\App\Core\Env::load(__DIR__ . '/../.env');

// Configure Error Logging
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/../logs/error.log');

// Only show errors to public if not on production
if (($_ENV['APP_ENV'] ?? 'production') === 'development') {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(0);
}

use App\Core\Router;

// 1. Clean the Incoming URI
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$scriptDir = str_replace('/public/index.php', '', $_SERVER['SCRIPT_NAME']);
$uri = str_replace($scriptDir, '', $requestUri);

// 2. Load Application Routes (this file returns a configured Router instance)
$router = require_once __DIR__ . '/../routes/web.php';

// 3. Dispatch the Request
$router->dispatch($uri);

ob_end_flush();
