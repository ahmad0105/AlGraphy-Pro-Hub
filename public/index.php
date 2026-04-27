<?php
declare(strict_types=1);

// Initialize Buffering & Errors
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', '0');
date_default_timezone_set('Asia/Riyadh');

// Load Composer Autoloader
require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;

// 1. Clean the Incoming URI
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$scriptDir = str_replace('/public/index.php', '', $_SERVER['SCRIPT_NAME']);
$uri = str_replace($scriptDir, '', $requestUri);

// 2. Initialize Router
$router = new Router();

// 3. Load Application Routes
require_once __DIR__ . '/../routes/web.php';

// 4. Dispatch the Request
$router->dispatch($uri);

ob_end_flush();
