<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Core\Config;
use Exception;

class AuthController extends Controller {
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function register(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $username = $this->sanitize($_POST['username'] ?? '');
                $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
                $password = $_POST['password'] ?? '';

                if (!$email) {
                    throw new Exception("Invalid email address.");
                }

                if (empty($username) || empty($password)) {
                    throw new Exception("All fields are required.");
                }

                if ($this->userModel->findByEmail($email) || $this->userModel->findByUsername($username)) {
                    throw new Exception("Username or Email already taken.");
                }

                if ($this->userModel->create($username, $email, $password)) {
                    $user = $this->userModel->findByEmail($email);
                    
                    // Generate OTP
                    $otp = (string)random_int(100000, 999999);
                    $expires = (new \DateTime())->modify('+15 minutes')->format('Y-m-d H:i:s');
                    $this->userModel->updateOTP((int)$user['id'], $otp, $expires);

                    // Send OTP via mail()
                    $subject = "Your Verification Code - AlGraphy Pro Hub";
                    $message = "Your 6-digit verification code is: " . $otp . "\nThis code expires in 15 minutes.";
                    $headers = "From: noreply@algraphyprohub.com";
                    @mail($email, $subject, $message, $headers);

                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    
                    $this->setFlash('success', "Registration successful! Please enter the OTP sent to your email.");
                    $this->redirect("/verify-otp");
                } else {
                    throw new Exception("Failed to create account.");
                }
            } catch (Exception $e) {
                $this->setFlash('error', $e->getMessage());
                $this->redirect("/register");
            }
        }
        $this->view('auth/register');
    }

    public function login(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
                $password = $_POST['password'] ?? '';

                if (!$email) {
                    throw new Exception("Please enter a valid email.");
                }

                $user = $this->userModel->findByEmail($email);

                if ($user && password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    
                    if (!$user['is_verified']) {
                        $this->redirect("/verify-otp");

                    }
                    
                    $this->redirect("/dashboard");
                } else {
                    throw new Exception("Invalid email or password.");
                }
            } catch (Exception $e) {
                $this->setFlash('error', $e->getMessage());
                $this->redirect("/login");
            }
        }
        $this->view('auth/login');
    }

    public function verify(): void {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect("/login");
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $otp = $this->sanitize($_POST['otp'] ?? '');
                $userId = (int)$_SESSION['user_id'];

                if ($this->userModel->verifyOTP($userId, $otp)) {
                    $this->userModel->markAsVerified($userId);
                    $this->setFlash('success', "Account verified successfully! Welcome to your dashboard.");
                    $this->redirect("/dashboard");
                } else {
                    throw new Exception("Invalid or expired OTP code.");
                }
            } catch (Exception $e) {
                $this->setFlash('error', $e->getMessage());
                $this->redirect("/verify-otp");
            }
        }
        $this->view('auth/verify');
    }

    public function resend(): void {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect("/login");
        }

        try {
            $userId = (int)$_SESSION['user_id'];
            $user = $this->userModel->findById($userId);

            if (!$user) {
                throw new Exception("User not found.");
            }

            // Generate new OTP
            $otp = (string)random_int(100000, 999999);
            $expires = (new \DateTime())->modify('+15 minutes')->format('Y-m-d H:i:s');
            $this->userModel->updateOTP($userId, $otp, $expires);

            // Send via mail()
            $subject = "Your NEW Verification Code - AlGraphy Pro Hub";
            $message = "Your new 6-digit verification code is: " . $otp . "\nThis code expires in 15 minutes.";
            $headers = "From: noreply@algraphyprohub.com";
            @mail($user['email'], $subject, $message, $headers);

            $this->setFlash('success', "A new verification code has been sent to your email.");
            $this->redirect("/verify-otp");
        } catch (Exception $e) {
            $this->setFlash('error', $e->getMessage());
            $this->redirect("/verify-otp");
        }
    }

    public function logout(): void {
        session_destroy();
        $this->redirect("/login");
    }

    public function forgotPassword(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
                if (!$email) {
                    throw new Exception("Invalid email address.");
                }

                $user = $this->userModel->findByEmail($email);

                if ($user) {
                    $token = bin2hex(random_bytes(32));
                    $expires = (new \DateTime())->modify('+15 minutes')->format('Y-m-d H:i:s');
                    $this->userModel->updateResetToken($email, $token, $expires);

                    $resetLink = "http://" . $_SERVER['HTTP_HOST'] . Config::url("reset-password?token=" . $token);
                    $subject = "Password Reset Request";
                    $message = "Click here to reset your password: " . $resetLink;
                    $headers = "From: noreply@algraphyprohub.com";

                    // Native mail() call
                    @mail($email, $subject, $message, $headers);
                }

                $this->setFlash('success', "If that email exists, a reset link has been sent.");
                $this->redirect("/forgot-password");
            } catch (Exception $e) {
                $this->setFlash('error', $e->getMessage());
                $this->redirect("/forgot-password");
            }
        }
        $this->view('auth/forgot-password');
    }

    public function resetPassword(): void {
        $token = $this->sanitize($_GET['token'] ?? '');
        if (empty($token) && $_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->redirect("/login");
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $password = $_POST['password'] ?? '';
                $token = $_POST['token'] ?? '';

                if (empty($password)) {
                    throw new Exception("Password cannot be empty.");
                }

                $user = $this->userModel->findByResetToken($token);

                if ($user) {
                    $this->userModel->updatePassword((int)$user['id'], $password);
                    $this->setFlash('success', "Password reset successful! Please log in.");
                    $this->redirect("/login");
                } else {
                    throw new Exception("Invalid or expired token.");
                }
            } catch (Exception $e) {
                $this->setFlash('error', $e->getMessage());
                $this->redirect("/reset-password?token=" . $token);
            }
        }
        $this->view('auth/reset-password', ['token' => $token]);
    }
}