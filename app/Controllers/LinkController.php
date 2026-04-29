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
                // Allow http, https, mailto, tel, starting with +/numbers (phone), or email format
                if (!preg_match('/^(https?:\/\/|mailto:|tel:|\+?[0-9]|[^\s@]+@[^\s@]+\.[^\s@]+)/i', $url)) {
                    throw new Exception("Please enter a valid URL, phone number, or email.");
                }

                // Use manual icon from picker, or fallback to smart detection
                $icon = $this->sanitize($_POST['icon'] ?? $this->getPlatformIcon($url));



                if ($this->linkModel->addLink((int)$_SESSION['user_id'], $title, $url, $icon, $subtitle)) {
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
                    throw new Exception("Please provide all required fields.");
                }

                // Validate URL protocol
                if (!preg_match('/^(https?:\/\/|mailto:|tel:|\+?[0-9]|[^\s@]+@[^\s@]+\.[^\s@]+)/i', $url)) {
                    throw new Exception("Please enter a valid URL, phone number, or email.");
                }

                // Use manual icon from picker, or fallback to smart detection
                $icon = $this->sanitize($_POST['icon'] ?? $this->getPlatformIcon($url));



                if ($this->linkModel->updateLink($id, (int)$_SESSION['user_id'], $title, $url, $icon, $subtitle)) {
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

}
