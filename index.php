<?php
/**
 * AlGraphy Pro Hub - Root Entry Point
 * This file is created specifically to bypass shared hosting strict requirements (like InfinityFree)
 * which explicitly demand an index.php file in the root htdocs directory.
 * It simply routes the traffic to our secure public folder.
 */

require_once __DIR__ . '/public/index.php';
