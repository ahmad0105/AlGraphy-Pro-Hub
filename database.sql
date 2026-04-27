-- AlGraphy Pro Hub - Database Schema
-- Optimized for MVC Pattern and Data Integrity

CREATE DATABASE IF NOT EXISTS algraphy_pro_hub;
USE algraphy_pro_hub;

-- ══════════════════════════════════════════
-- 1. USERS TABLE
-- ══════════════════════════════════════════
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    display_name VARCHAR(100) DEFAULT NULL,
    bio VARCHAR(80) DEFAULT NULL,
    avatar VARCHAR(255) DEFAULT NULL,
    
    -- Appearance Engine
    theme VARCHAR(50) DEFAULT 'dark',
    bg_type VARCHAR(20) DEFAULT 'color',
    bg_color VARCHAR(20) DEFAULT '#000000',
    bg_media VARCHAR(255) DEFAULT NULL,
    button_bg_color VARCHAR(20) DEFAULT '#dc2726',
    button_text_color VARCHAR(20) DEFAULT '#ffffff',
    font_color VARCHAR(20) DEFAULT '#ffffff',
    button_radius VARCHAR(20) DEFAULT '12px',
    font_family VARCHAR(100) DEFAULT "'Nunito', sans-serif",
    
    -- SEO & Security
    seo_title VARCHAR(255) DEFAULT NULL,
    seo_description TEXT DEFAULT NULL,
    is_verified BOOLEAN DEFAULT FALSE,
    otp_code VARCHAR(10) DEFAULT NULL,
    otp_expires_at DATETIME DEFAULT NULL,
    reset_token VARCHAR(255) DEFAULT NULL,
    token_expires DATETIME DEFAULT NULL,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ══════════════════════════════════════════
-- 2. USER SOCIALS TABLE
-- ══════════════════════════════════════════
CREATE TABLE IF NOT EXISTS user_socials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    platform VARCHAR(50) NOT NULL,
    value VARCHAR(255) DEFAULT NULL,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY user_platform (user_id, platform),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ══════════════════════════════════════════
-- 3. LINKS TABLE
-- Relationship: A Link belongs to a User
-- ══════════════════════════════════════════
CREATE TABLE IF NOT EXISTS links (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    subtitle VARCHAR(255) DEFAULT NULL,
    url TEXT NOT NULL,
    icon VARCHAR(100) DEFAULT 'fas fa-link',
    thumbnail VARCHAR(255) DEFAULT NULL,
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ══════════════════════════════════════════
-- 3. PAGE VIEWS (Analytics)
-- Relationship: A View belongs to a User (Profile)
-- ══════════════════════════════════════════
CREATE TABLE IF NOT EXISTS page_views (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    user_agent TEXT NOT NULL,
    viewed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ══════════════════════════════════════════
-- 4. LINK CLICKS (Analytics)
-- Relationship: A Click belongs to a Link
-- ══════════════════════════════════════════
CREATE TABLE IF NOT EXISTS link_clicks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    link_id INT NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    user_agent TEXT NOT NULL,
    clicked_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (link_id) REFERENCES links(id) ON DELETE CASCADE
);
