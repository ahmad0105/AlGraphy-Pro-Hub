<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Analytics {
    private PDO $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    /**
     * Log a profile page view
     */
    public function logView(int $userId, string $ip, string $userAgent): void {
        $stmt = $this->db->prepare("INSERT INTO page_views (user_id, ip_address, user_agent) VALUES (?, ?, ?)");
        $stmt->execute([$userId, $ip, $userAgent]);
    }

    /**
     * Log a link click
     */
    public function logClick(int $linkId, string $ip, string $userAgent): void {
        $stmt = $this->db->prepare("INSERT INTO link_clicks (link_id, ip_address, user_agent) VALUES (?, ?, ?)");
        $stmt->execute([$linkId, $ip, $userAgent]);
    }

    /**
     * Get statistics for a user
     */
    public function getUserStats(int $userId): array {
        // Total Views
        $stmt = $this->db->prepare("SELECT COUNT(*) as total_views FROM page_views WHERE user_id = ?");
        $stmt->execute([$userId]);
        $totalViews = $stmt->fetch(PDO::FETCH_ASSOC)['total_views'];

        // Unique Views (by IP)
        $stmt = $this->db->prepare("SELECT COUNT(DISTINCT ip_address) as unique_views FROM page_views WHERE user_id = ?");
        $stmt->execute([$userId]);
        $uniqueViews = $stmt->fetch(PDO::FETCH_ASSOC)['unique_views'];

        // Total Clicks
        $stmt = $this->db->prepare("SELECT COUNT(lc.id) as total_clicks 
                                   FROM link_clicks lc 
                                   JOIN links l ON lc.link_id = l.id 
                                   WHERE l.user_id = ?");
        $stmt->execute([$userId]);
        $totalClicks = $stmt->fetch(PDO::FETCH_ASSOC)['total_clicks'];

        return [
            'total_views' => $totalViews,
            'unique_views' => $uniqueViews,
            'total_clicks' => $totalClicks
        ];
    }
}
