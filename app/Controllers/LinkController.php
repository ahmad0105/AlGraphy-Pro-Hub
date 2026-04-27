<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Link;
use Exception;

class LinkController extends Controller {
    private Link $linkModel;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                exit;
            }
            $this->redirect("/login");
        }
        $this->linkModel = new Link();
    }

    /**
     * Add a new link via POST
     */
    public function add(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $title = $this->sanitize($_POST['title'] ?? '');
                $subtitle = $this->sanitize($_POST['subtitle'] ?? '');
                $url = filter_var(trim($_POST['url'] ?? ''), FILTER_SANITIZE_URL);

                if (!$title || !$url) {
                    throw new Exception("Please provide a valid title and URL.");
                }

                // Validate URL protocol to prevent javascript: XSS
                if (!preg_match('/^(https?:\/\/|mailto:)/i', $url)) {
                    throw new Exception("URL must start with http:// or https://");
                }

                // Use manual icon from picker, or fallback to smart detection
                $icon = $this->sanitize($_POST['icon'] ?? $this->getPlatformIcon($url));

                // Handle Thumbnail Upload
                $thumbnail = null;
                if (!empty($_FILES['thumbnail']['name'])) {
                    $thumbnail = $this->handleThumbnailUpload((int)$_SESSION['user_id']);
                }

                if ($this->linkModel->addLink((int)$_SESSION['user_id'], $title, $url, $icon, $subtitle, $thumbnail)) {
                    $this->setFlash('success', "Link added successfully!");
                } else {
                    throw new Exception("Failed to add link.");
                }
            } catch (Exception $e) {
                $this->setFlash('error', $e->getMessage());
            }
        }
        $this->redirect("/dashboard");
    }

    /**
     * Update an existing link via POST
     */
    public function update(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $id = (int)($_POST['id'] ?? 0);
                $title = $this->sanitize($_POST['title'] ?? '');
                $subtitle = $this->sanitize($_POST['subtitle'] ?? '');
                $url = filter_var(trim($_POST['url'] ?? ''), FILTER_SANITIZE_URL);

                if (!$id || !$title || !$url) {
                    throw new Exception("Missing required fields.");
                }

                // Validate URL protocol to prevent javascript: XSS
                if (!preg_match('/^(https?:\/\/|mailto:)/i', $url)) {
                    throw new Exception("URL must start with http:// or https://");
                }

                // Use manual icon from picker, or fallback to smart detection
                $icon = $this->sanitize($_POST['icon'] ?? $this->getPlatformIcon($url));

                // Handle Thumbnail Upload
                $thumbnail = $_POST['current_thumbnail'] ?? null;
                if (!empty($_FILES['thumbnail']['name'])) {
                    $thumbnail = $this->handleThumbnailUpload((int)$_SESSION['user_id']);
                }

                if ($this->linkModel->updateLink($id, (int)$_SESSION['user_id'], $title, $url, $icon, $subtitle, $thumbnail)) {
                    $this->setFlash('success', "Link updated successfully!");
                } else {
                    throw new Exception("Failed to update link.");
                }
            } catch (Exception $e) {
                $this->setFlash('error', $e->getMessage());
            }
        }
        $this->redirect("/dashboard");
    }

    /**
     * Toggle link status via AJAX
     */
    public function toggle(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)($_POST['id'] ?? 0);
            if ($id && $this->linkModel->toggleStatus($id, (int)$_SESSION['user_id'])) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true]);
                return;
            }
        }
        header('Content-Type: application/json');
        echo json_encode(['success' => false]);
    }

    /**
     * Delete a link
     */
    public function delete(): void {
        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) {
            if ($this->linkModel->deleteLink($id, (int)$_SESSION['user_id'])) {
                $this->setFlash('success', "Link deleted successfully!");
            } else {
                $this->setFlash('error', "Failed to delete link.");
            }
        }
        $this->redirect("/dashboard");
    }

    /**
     * Update links order via AJAX
     */
    public function updateOrder(): void {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $order = $input['order'] ?? [];

        if (empty($order)) {
            echo json_encode(['success' => false, 'message' => 'No order data provided']);
            return;
        }

        if ($this->linkModel->updateOrder((int)$_SESSION['user_id'], $order)) {
            echo json_encode(['success' => true, 'message' => 'Order updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update order']);
        }
    }

    /**
     * Handle link thumbnail upload
     */
    private function handleThumbnailUpload(int $userId): string {
        $file = $_FILES['thumbnail'];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        $maxSize = 2 * 1024 * 1024; // 2MB

        if (!in_array($file['type'], $allowedTypes)) {
            throw new Exception("Thumbnail must be a JPG, PNG or WebP image.");
        }

        if ($file['size'] > $maxSize) {
            throw new Exception("Thumbnail is too large. Max 2MB.");
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = 'thumb_' . $userId . '_' . time() . '_' . rand(100, 999) . '.' . $extension;
        $uploadPath = __DIR__ . '/../../public/uploads/links/' . $fileName;

        // Ensure directory exists
        if (!is_dir(dirname($uploadPath))) {
            mkdir(dirname($uploadPath), 0777, true);
        }

        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            return $fileName;
        } else {
            throw new Exception("Failed to save thumbnail.");
        }
    }
}
