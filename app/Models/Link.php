<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Link {
    private PDO $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    /**
     * Get all links for a specific user,
     * ordered by sort_order
     */
    public function getLinksByUserId($userId, $activeOnly = false) {
        $sql = "SELECT * FROM links WHERE user_id = ?";
        if ($activeOnly) {
            $sql .= " AND is_active = 1";
        }
        $sql .= " ORDER BY sort_order ASC, created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Find a single link by ID
     */
    public function findById(int $id) {
        $stmt = $this->db->prepare("SELECT * FROM links WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Toggle link active status
     */
    public function toggleStatus($linkId, $userId) {
        $stmt = $this->db->prepare("UPDATE links SET is_active = 1 - is_active WHERE id = ? AND user_id = ?");
        return $stmt->execute([$linkId, $userId]);
    }

    /**
     * Add a new link
     */
    public function addLink($userId, $title, $url, $icon = 'fas fa-link', $subtitle = null) {
        // Get the current highest sort_order to put the new link at the end
        $stmt = $this->db->prepare("SELECT MAX(sort_order) as max_order FROM links WHERE user_id = ?");
        $stmt->execute([$userId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $nextOrder = ($row['max_order'] ?? -1) + 1;

        $stmt = $this->db->prepare("INSERT INTO links (user_id, title, subtitle, url, icon, sort_order) VALUES (?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$userId, $title, $subtitle, $url, $icon, $nextOrder]);
    }

    /**
     * Delete a link
     */
    public function deleteLink($linkId, $userId) {
        $stmt = $this->db->prepare("DELETE FROM links WHERE id = ? AND user_id = ?");
        return $stmt->execute([$linkId, $userId]);
    }

    /**
     * Update an existing link
     */
    public function updateLink($linkId, $userId, $title, $url, $icon, $subtitle = null) {
        $stmt = $this->db->prepare("UPDATE links SET title = ?, subtitle = ?, url = ?, icon = ? WHERE id = ? AND user_id = ?");
        return $stmt->execute([$title, $subtitle, $url, $icon, $linkId, $userId]);
    }

    /**
     * Update the sort order of links
     */
    public function updateOrder($userId, $orderData) {
        $this->db->beginTransaction();
        try {
            foreach ($orderData as $index => $linkId) {
                $stmt = $this->db->prepare("UPDATE links SET sort_order = ? WHERE id = ? AND user_id = ?");
                $stmt->execute([$index, $linkId, $userId]);
            }
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
}
