<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

class User {
    private PDO $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    public function findByEmail(string $email): ?array {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $result = $stmt->fetch();
        if ($result) {
            $result['socials'] = $this->getSocials((int)$result['id']);
        }
        return $result ?: null;
    }

    public function findByUsername(string $username): ?array {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $result = $stmt->fetch();
        if ($result) {
            $result['socials'] = $this->getSocials((int)$result['id']);
        }
        return $result ?: null;
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        if ($result) {
            $result['socials'] = $this->getSocials($id);
        }
        return $result ?: null;
    }

    public function getSocials(int $userId): array {
        $stmt = $this->db->prepare("SELECT platform, value, sort_order FROM user_socials WHERE user_id = :user_id ORDER BY sort_order ASC");
        $stmt->execute(['user_id' => $userId]);
        $socials = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $formatted = [];
        foreach ($socials as $social) {
            $formatted[$social['platform']] = $social['value'];
        }
        // Add a helper for the order
        $formatted['_order'] = array_column($socials, 'platform');
        return $formatted;
    }

    public function create(string $username, string $email, string $password): bool {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
        return $stmt->execute([
            'username' => $username,
            'email' => $email,
            'password' => $hashedPassword
        ]);
    }

    public function updateResetToken(string $email, string $token, string $expires): bool {
        $stmt = $this->db->prepare("UPDATE users SET reset_token = :token, token_expires = :expires WHERE email = :email");
        return $stmt->execute([
            'token' => $token,
            'expires' => $expires,
            'email' => $email
        ]);
    }

    public function findByResetToken(string $token): ?array {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE reset_token = :token AND token_expires > NOW()");
        $stmt->execute(['token' => $token]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function updatePassword(int $id, string $password): bool {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("UPDATE users SET password = :password, reset_token = NULL, token_expires = NULL WHERE id = :id");
        return $stmt->execute([
            'password' => $hashedPassword,
            'id' => $id
        ]);
    }

    public function updateOTP(int $id, string $otp, string $expires): bool {
        $stmt = $this->db->prepare("UPDATE users SET otp_code = :otp, otp_expires_at = :expires WHERE id = :id");
        return $stmt->execute([
            'otp' => $otp,
            'expires' => $expires,
            'id' => $id
        ]);
    }

    public function verifyOTP(int $id, string $otp): bool {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id AND otp_code = :otp AND otp_expires_at > NOW()");
        $stmt->execute(['id' => $id, 'otp' => $otp]);
        return (bool)$stmt->fetch();
    }

    public function markAsVerified(int $id): bool {
        $stmt = $this->db->prepare("UPDATE users SET is_verified = 1, otp_code = NULL, otp_expires_at = NULL WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function updateProfileDetails(int $id, string $displayName, string $bio): bool {
        $stmt = $this->db->prepare("UPDATE users SET display_name = :display_name, bio = :bio WHERE id = :id");
        return $stmt->execute([
            'display_name' => $displayName,
            'bio' => $bio,
            'id' => $id
        ]);
    }

    public function updateAvatar(int $id, string $avatar): bool {
        $stmt = $this->db->prepare("UPDATE users SET avatar = :avatar WHERE id = :id");
        return $stmt->execute([
            'avatar' => $avatar,
            'id' => $id
        ]);
    }

    public function updateBasicInfo(int $id, string $email, string $username): bool {
        $stmt = $this->db->prepare("UPDATE users SET email = :email, username = :username WHERE id = :id");
        return $stmt->execute([
            'email' => $email,
            'username' => $username,
            'id' => $id
        ]);
    }

    public function updateAppearance(int $id, array $data): bool {
        $sql = "UPDATE users SET 
                theme = :theme, 
                bg_color = :bg_color, 
                button_bg_color = :button_bg_color, 
                button_text_color = :button_text_color, 
                font_color = :font_color, 
                button_radius = :button_radius, 
                font_family = :font_family,
                bg_type = :bg_type
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        $data['id'] = $id;
        return $stmt->execute($data);
    }

    public function updateBackgroundType(int $id, string $type): bool {
        $stmt = $this->db->prepare("UPDATE users SET bg_type = :type WHERE id = :id");
        return $stmt->execute(['type' => $type, 'id' => $id]);
    }

    public function updateBackgroundMedia(int $id, string $bgMedia): bool {
        $stmt = $this->db->prepare("UPDATE users SET bg_media = :bg_media WHERE id = :id");
        return $stmt->execute([
            'bg_media' => $bgMedia,
            'id' => $id
        ]);
    }

    public function updateSEO(int $id, string $title, string $description): bool {
        $stmt = $this->db->prepare("UPDATE users SET seo_title = :title, seo_description = :description WHERE id = :id");
        return $stmt->execute([
            'title' => $title,
            'description' => $description,
            'id' => $id
        ]);
    }

    public function updateSocialField(int $id, string $field, string $value): bool {
        // Change twitter to x if necessary
        if ($field === 'twitter') $field = 'x';
        
        $stmt = $this->db->prepare("INSERT INTO user_socials (user_id, platform, value) 
                                    VALUES (:user_id, :platform, :value) 
                                    ON DUPLICATE KEY UPDATE value = :value2");
        return $stmt->execute([
            'user_id' => $id,
            'platform' => $field,
            'value' => $value,
            'value2' => $value
        ]);
    }

    public function updateSocialOrder(int $id, array $order): bool {
        try {
            $this->db->beginTransaction();
            foreach ($order as $index => $platform) {
                if ($platform === 'twitter') $platform = 'x';
                // Use INSERT ON DUPLICATE KEY UPDATE to ensure order is saved even if value is empty
                $stmt = $this->db->prepare("INSERT INTO user_socials (user_id, platform, sort_order, value) 
                                            VALUES (:user_id, :platform, :sort_order, '') 
                                            ON DUPLICATE KEY UPDATE sort_order = :sort_order2");
                $stmt->execute([
                    'sort_order' => $index,
                    'user_id' => $id,
                    'platform' => $platform,
                    'sort_order2' => $index
                ]);
            }
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
}