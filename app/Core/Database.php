<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;

class Database {
    private static ?PDO $connection = null;

    public static function connect(): PDO {
        if (self::$connection === null) {
            $host = $_ENV['DB_HOST'] ?? "localhost";
            $db_name = $_ENV['DB_NAME'] ?? "algraphy_pro_hub";
            $username = $_ENV['DB_USER'] ?? "root";
            $password = $_ENV['DB_PASS'] ?? "";

            try {
                self::$connection = new PDO(
                    "mysql:host=$host;dbname=$db_name;charset=utf8mb4", 
                    $username, 
                    $password,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                    ]
                );
            } catch (PDOException $exception) {
                error_log("Database connection error: " . $exception->getMessage());
                die("A database error occurred. Please check the logs.");
            }
        }
        return self::$connection;
    }
}
