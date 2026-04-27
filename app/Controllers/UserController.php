<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use Exception;

class UserController extends Controller {
    
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Account Settings
     */
    public function settings(): void {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect("/login");
        }

        $userId = (int)$_SESSION['user_id'];
        $user = $this->userModel->findById($userId);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // 1. Update Basic Info (Email & Username)
                $email = $this->sanitize($_POST['email']);
                $username = $this->sanitize($_POST['username']);
                $this->userModel->updateBasicInfo($userId, $email, $username);

                // 2. Update Profile Details (Display Name, Bio & Socials)
                $displayName = $this->sanitize($_POST['display_name']);
                $bio = $this->sanitize($_POST['bio']);
                $instagram = $this->sanitize($_POST['instagram'] ?? '');
                $twitter = $this->sanitize($_POST['twitter'] ?? '');
                $linkedin = $this->sanitize($_POST['linkedin'] ?? '');
                $whatsapp = $this->sanitize($_POST['whatsapp'] ?? '');
                $publicEmail = $this->sanitize($_POST['public_email'] ?? '');
                $threads = $this->sanitize($_POST['threads'] ?? '');
                $tiktok = $this->sanitize($_POST['tiktok'] ?? '');
                $facebook = $this->sanitize($_POST['facebook'] ?? '');
                $youtube = $this->sanitize($_POST['youtube'] ?? '');
                $snapchat = $this->sanitize($_POST['snapchat'] ?? '');

                // Ensure bio is max 80 characters
                $bio = mb_substr($bio, 0, 80);
                $this->userModel->updateProfileDetails($userId, $displayName, $bio);
                
                // Update socials if provided individually
                $socials = ['instagram', 'twitter', 'linkedin', 'whatsapp', 'public_email', 'threads', 'tiktok', 'facebook', 'youtube', 'snapchat'];
                foreach ($socials as $key) {
                    if (isset($_POST[$key])) {
                        $this->userModel->updateSocialField($userId, $key, $this->sanitize($_POST[$key]));
                    }
                }

                // 3. Update Password (If provided)
                if (!empty($_POST['password'])) {
                    $this->userModel->updatePassword($userId, $_POST['password']);
                }

                // 4. Update Appearance Settings
                if (isset($_POST['theme'])) {
                    // Whitelist allowed theme values
                    $allowedThemes = ['dark', 'light', 'ocean', 'neon', 'custom'];
                    $theme = in_array($_POST['theme'], $allowedThemes) ? $_POST['theme'] : 'dark';

                    // Whitelist allowed font families
                    $allowedFonts = ["'Nunito', sans-serif", "'Inter', sans-serif", "'Roboto', sans-serif", "'Outfit', sans-serif"];
                    $fontFamily = in_array($_POST['font_family'] ?? '', $allowedFonts) ? $_POST['font_family'] : "'Nunito', sans-serif";

                    // Whitelist allowed bg_type
                    $allowedBgTypes = ['color', 'image', 'video', 'media'];
                    $bgTypeInput = in_array($_POST['bg_type'] ?? 'color', $allowedBgTypes) ? $_POST['bg_type'] : 'color';

                    // If 'media' is selected, we need to know the actual type (existing or new)
                    if ($bgTypeInput === 'media') {
                        $currentUser = $this->userModel->findById($userId);
                        $bgType = in_array($currentUser['bg_type'], ['image', 'video']) ? $currentUser['bg_type'] : 'image';
                    } else {
                        $bgType = $bgTypeInput;
                    }

                    $appearanceData = [
                        'theme' => $theme,
                        'bg_color' => $this->sanitizeColor($_POST['bg_color'] ?? '#000000'),
                        'button_bg_color' => $this->sanitizeColor($_POST['button_bg_color'] ?? '#dc2726'),
                        'button_text_color' => $this->sanitizeColor($_POST['button_text_color'] ?? '#ffffff'),
                        'font_color' => $this->sanitizeColor($_POST['font_color'] ?? '#ffffff'),
                        'button_radius' => $this->sanitizeCSSValue($_POST['button_radius'] ?? '12px'),
                        'font_family' => $fontFamily,
                        'bg_type' => $bgType
                    ];
                    $this->userModel->updateAppearance($userId, $appearanceData);
                }

                // Handle Background Media Upload
                if (!empty($_FILES['bg_media']['name']) && in_array($_POST['bg_type'] ?? '', ['image', 'video', 'media'])) {
                    $this->handleBackgroundUpload($userId);
                }

                // 5. Update SEO Settings
                if (isset($_POST['seo_title'])) {
                    $seoTitle = $this->sanitize($_POST['seo_title']);
                    $seoDesc = $this->sanitize($_POST['seo_description']);
                    $this->userModel->updateSEO($userId, $seoTitle, $seoDesc);
                }

                // 6. Handle Avatar Upload
                if (!empty($_FILES['avatar']['name'])) {
                    $this->handleAvatarUpload($userId);
                }

                // Update Session info
                $_SESSION['username'] = $username;

                $this->setFlash('success', 'Your settings have been updated successfully.');
                $this->redirect("/settings");

            } catch (Exception $e) {
                $this->setFlash('error', $e->getMessage());
                $this->redirect("/settings");
            }
        }

        $this->view('dashboard/settings', ['user' => $user]);
    }

    /**
     * Handle avatar file upload
     */
    private function handleAvatarUpload(int $userId): void {
        $file = $_FILES['avatar'];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        $maxSize = 2 * 1024 * 1024; // 2MB

        if (!in_array($file['type'], $allowedTypes)) {
            throw new Exception("Avatar type not allowed. Please use JPG, PNG or WebP.");
        }

        if ($file['size'] > $maxSize) {
            throw new Exception("Avatar is too large. Maximum size is 2MB.");
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = 'avatar_' . $userId . '_' . time() . '.' . $extension;
        $uploadPath = __DIR__ . '/../../public/uploads/avatars/' . $fileName;

        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            $user = $this->userModel->findById($userId);
            if ($user['avatar']) {
                $oldPath = __DIR__ . '/../../public/uploads/avatars/' . $user['avatar'];
                if (file_exists($oldPath)) { @unlink($oldPath); }
            }
            $this->userModel->updateAvatar($userId, $fileName);
        } else {
            throw new Exception("Failed to save avatar. Check folder permissions.");
        }
    }

    /**
     * Handle background media upload
     */
    private function handleBackgroundUpload(int $userId): void {
        $file = $_FILES['bg_media'];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'video/mp4'];
        $maxSize = 10 * 1024 * 1024; // 10MB limit for background images and short video loops

        if (!in_array($file['type'], $allowedTypes)) {
            throw new Exception("Background file type not allowed. Please use JPG, PNG, WebP, or MP4.");
        }

        if ($file['size'] > $maxSize) {
            throw new Exception("Background file is too large. Maximum size is 10MB.");
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = 'bg_' . $userId . '_' . time() . '.' . $extension;
        $uploadPath = __DIR__ . '/../../public/uploads/backgrounds/' . $fileName;

        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            $user = $this->userModel->findById($userId);
            if (!empty($user['bg_media'])) {
                $oldPath = __DIR__ . '/../../public/uploads/backgrounds/' . $user['bg_media'];
                if (file_exists($oldPath)) { @unlink($oldPath); }
            }
            
            // Automatically determine and update bg_type based on MIME type
            $detectedType = str_contains($file['type'], 'video') ? 'video' : 'image';
            
            $this->userModel->updateBackgroundMedia($userId, $fileName);
            
            // Also update the bg_type column to match the uploaded file
            $this->userModel->updateBackgroundType($userId, $detectedType);
        } else {
            throw new Exception("Failed to save background media. Check folder permissions.");
        }
    }

    public function updateSocial(): void {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        $userId = $_SESSION['user_id'];
        $key = $_POST['key'] ?? '';
        $value = $this->sanitize($_POST['value'] ?? '');

        if (empty($key)) {
            echo json_encode(['success' => false, 'message' => 'Missing platform key']);
            return;
        }

        if ($this->userModel->updateSocialField($userId, $key, $value)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database error']);
        }
    }

    public function reorderSocials(): void {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        $userId = $_SESSION['user_id'];
        $order = $_POST['order'] ?? null;

        if ($order && $this->userModel->updateSocialOrder($userId, $order)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to save order']);
        }
    }
}
