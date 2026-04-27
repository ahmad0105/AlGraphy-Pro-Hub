<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <?php
    // SEO & Open Graph Logic
    $seoTitle = !empty($user['seo_title']) ? $user['seo_title'] : ($user['display_name'] ?: $user['username']) . ' | AlGraphy Pro Hub';
    $seoDesc = !empty($user['seo_description']) ? $user['seo_description'] : (!empty($user['bio']) ? $user['bio'] : 'Check out my professional AlGraphy Pro Hub profile.');
    $profileUrl = "https://" . $_SERVER['HTTP_HOST'] . "/" . $user['username'];
    $avatarUrl = !empty($user['avatar']) ? \App\Core\Config::url('public/uploads/avatars/' . $user['avatar']) : \App\Core\Config::asset('img/default-avatar.png');
    ?>

    <!-- Performance: Preload Avatar -->
    <link rel="preload" as="image" href="<?php echo $avatarUrl; ?>">

    <!-- Standard Meta Tags -->
    <title><?php echo htmlspecialchars($seoTitle); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($seoDesc); ?>">
    <link rel="canonical" href="<?php echo $profileUrl; ?>">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="profile">
    <meta property="og:url" content="<?php echo $profileUrl; ?>">
    <meta property="og:title" content="<?php echo htmlspecialchars($seoTitle); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($seoDesc); ?>">
    <meta property="og:image" content="<?php echo $avatarUrl; ?>">
    <meta property="og:image:width" content="600">
    <meta property="og:image:height" content="600">
    <meta property="og:locale" content="en_US">
    <meta property="og:site_name" content="AlGraphy Pro Hub">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="<?php echo $profileUrl; ?>">
    <meta property="twitter:title" content="<?php echo htmlspecialchars($seoTitle); ?>">
    <meta property="twitter:description" content="<?php echo htmlspecialchars($seoDesc); ?>">
    <meta property="twitter:image" content="<?php echo $avatarUrl; ?>">

    <!-- Favicons -->
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo $avatarUrl; ?>">

    <!-- Structured Data: JSON-LD -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "ProfilePage",
      "mainEntity": {
        "@type": "Person",
        "name": "<?php echo htmlspecialchars($user['display_name'] ?: $user['username']); ?>",
        "alternateName": "@<?php echo htmlspecialchars($user['username']); ?>",
        "description": "<?php echo htmlspecialchars($seoDesc); ?>",
        "image": "<?php echo $avatarUrl; ?>",
        "url": "<?php echo $profileUrl; ?>",
        "sameAs": [
          "<?php echo !empty($user['instagram']) ? 'https://instagram.com/' . htmlspecialchars($user['instagram']) : ''; ?>",
          "<?php echo !empty($user['twitter']) ? 'https://twitter.com/' . htmlspecialchars($user['twitter']) : ''; ?>",
          "<?php echo !empty($user['linkedin']) ? 'https://linkedin.com/in/' . htmlspecialchars($user['linkedin']) : ''; ?>"
        ]
      }
    }
    </script>

    <!-- Core Styles -->
    <link rel="stylesheet" href="<?php echo \App\Core\Config::asset('css/style.css'); ?>">
    <!-- Profile Specific Styles -->
    <link rel="stylesheet" href="<?php echo \App\Core\Config::asset('css/profile.css'); ?>">
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <?php
    // Theme Engine Logic
    $theme = $user['theme'] ?? 'dark';

    // Validate all CSS values with regex to prevent CSS injection
    $bg = preg_match('/^#([A-Fa-f0-9]{3,8})$/', $user['bg_color'] ?? '') ? $user['bg_color'] : '#000000';
    $btnBg = preg_match('/^#([A-Fa-f0-9]{3,8})$/', $user['button_bg_color'] ?? '') ? $user['button_bg_color'] : '#dc2726';
    $btnText = preg_match('/^#([A-Fa-f0-9]{3,8})$/', $user['button_text_color'] ?? '') ? $user['button_text_color'] : '#ffffff';
    $fontColor = preg_match('/^#([A-Fa-f0-9]{3,8})$/', $user['font_color'] ?? '') ? $user['font_color'] : '#ffffff';
    $radius = preg_match('/^\d{1,3}(px|rem|em|%)$/', $user['button_radius'] ?? '') ? $user['button_radius'] : '20px';
    
    $allowedFonts = ["'Nunito', sans-serif", "'Inter', sans-serif", "'Roboto', sans-serif", "'Outfit', sans-serif"];
    $fontFamily = in_array($user['font_family'] ?? '', $allowedFonts) ? $user['font_family'] : "'Nunito', sans-serif";
    
    $bgType = in_array($user['bg_type'] ?? 'color', ['color', 'image', 'video']) ? ($user['bg_type'] ?? 'color') : 'color';
    $bgMedia = !empty($user['bg_media']) ? \App\Core\Config::url('public/uploads/backgrounds/' . $user['bg_media']) : null;

    // Fallback: If user selects image/video but forgot to upload a file, default back to color/matrix
    if (($bgType === 'image' || $bgType === 'video') && !$bgMedia) {
        $bgType = 'color';
    }

    // Values are now driven directly by database columns, seeded by JS presets in settings.
    $bg = $user['bg_color'] ?? '#000000';
    $btnBg = $user['button_bg_color'] ?? '#dc2726';
    $btnText = $user['button_text_color'] ?? '#ffffff';
    $fontColor = $user['font_color'] ?? '#ffffff';

    // Helper to format social URLs
    function formatSocialUrl($value, $prefix) {
        if (empty($value)) return '#';
        // Handle Email case
        if ($prefix === 'mailto:') {
            return 'mailto:' . htmlspecialchars($value);
        }
        // If it's already a full URL, return as is
        if (strpos($value, 'http://') === 0 || strpos($value, 'https://') === 0) {
            return htmlspecialchars($value);
        }
        // Otherwise, append the prefix (base URL)
        return $prefix . htmlspecialchars($value);
    }
    ?>

    <!-- Google Fonts Injection -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&family=Outfit:wght@400;700&family=Roboto:wght@400;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-color: <?php echo $btnBg; ?>;
            --bg-color: <?php echo $bg; ?>;
            --font-color: <?php echo $fontColor; ?>;
            --btn-text: <?php echo $btnText; ?>;
            --btn-radius: <?php echo $radius; ?>;
            --font-main: <?php echo $fontFamily; ?>;
        }

        body {
            background-color: var(--bg-color) !important;
            <?php if ($bgType === 'image' && $bgMedia): ?>
            background-image: url('<?php echo $bgMedia; ?>') !important;
            background-size: cover !important;
            background-position: center !important;
            background-attachment: fixed !important;
            <?php endif; ?>
            color: var(--font-color) !important;
            font-family: var(--font-main) !important;
        }

        .link {
            border-radius: var(--btn-radius) !important;
            color: var(--btn-text) !important;
            border: 1px solid var(--primary-color) !important;
        }

        .username, .display-name, .bio-text {
            color: var(--font-color) !important;
        }

        .social-media-container {
            background: rgba(255, 255, 255, 0.05) !important;
            backdrop-filter: blur(10px);
            border: 1px solid var(--primary-color) !important;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        .social-media-container a {
            background: transparent !important;
            color: var(--primary-color) !important;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .social-media-container a:hover {
            background: var(--primary-color) !important;
            color: var(--btn-text) !important;
            border-color: var(--primary-color) !important;
            box-shadow: 0 0 15px var(--primary-color);
        }
        #matrix-canvas {
            display: <?php echo (($theme === 'dark' || $theme === 'custom') && $bgType === 'color') ? 'block' : 'none'; ?>;
        }

        .link-icon {
            font-size: 1.4rem;
            position: absolute;
            left: 20px;
        }

        .link-thumb {
            width: 35px;
            height: 35px;
            border-radius: 8px;
            object-fit: cover;
            position: absolute;
            left: 15px;
            border: 1px solid rgba(255,255,255,0.1);
        }

        .link-content {
            padding-left: 50px;
        }

        .video-bg {
            position: fixed;
            top: 50%;
            left: 50%;
            min-width: 100%;
            min-height: 100%;
            width: auto;
            height: auto;
            z-index: -2;
            transform: translateX(-50%) translateY(-50%);
            object-fit: cover;
        }

        .bg-overlay {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.6); /* Dark overlay to make text readable */
            z-index: -1;
            display: <?php echo ($bgType === 'video' || $bgType === 'image') ? 'block' : 'none'; ?>;
        }
    </style>
</head>
<body>
    <canvas id="matrix-canvas"></canvas>

    <?php if ($bgType === 'video' && $bgMedia): ?>
        <video class="video-bg" autoplay loop muted playsinline>
            <source src="<?php echo $bgMedia; ?>" type="video/mp4">
        </video>
    <?php endif; ?>
    
    <div class="bg-overlay"></div>

    <div class="container">
        <div class="profile-avatar">
            <?php if (!empty($user['avatar'])): ?>
                <img src="<?php echo \App\Core\Config::url('public/uploads/avatars/' . $user['avatar']); ?>" alt="Profile">
            <?php else: ?>
                <i class="fa-solid fa-circle-user"></i>
            <?php endif; ?>
        </div>

        <h1 class="display-name"><?php echo htmlspecialchars($user['display_name'] ?: $user['username']); ?></h1>
        <?php if (!empty($user['bio'])): ?>
            <p class="bio-text"><?php echo nl2br(htmlspecialchars($user['bio'])); ?></p>
        <?php endif; ?>

        <div class="info-container">
            <!-- Social Handles Row -->
            <div class="social-media-container">
                <?php 
                $allSocials = [
                    'instagram' => ['icon' => 'fab fa-instagram', 'prefix' => 'https://instagram.com/', 'title' => 'Instagram'],
                    'x' => ['icon' => 'fab fa-x-twitter', 'prefix' => 'https://twitter.com/', 'title' => 'X (Twitter)'],
                    'twitter' => ['icon' => 'fab fa-x-twitter', 'prefix' => 'https://twitter.com/', 'title' => 'X (Twitter)'],
                    'tiktok' => ['icon' => 'fab fa-tiktok', 'prefix' => 'https://tiktok.com/@', 'title' => 'TikTok'],
                    'snapchat' => ['icon' => 'fab fa-snapchat', 'prefix' => 'https://snapchat.com/add/', 'title' => 'Snapchat'],
                    'facebook' => ['icon' => 'fab fa-facebook-f', 'prefix' => 'https://facebook.com/', 'title' => 'Facebook'],
                    'youtube' => ['icon' => 'fab fa-youtube', 'prefix' => 'https://youtube.com/@', 'title' => 'YouTube'],
                    'threads' => ['icon' => 'fab fa-threads', 'prefix' => 'https://threads.net/@', 'title' => 'Threads'],
                    'linkedin' => ['icon' => 'fab fa-linkedin-in', 'prefix' => 'https://linkedin.com/in/', 'title' => 'LinkedIn'],
                    'whatsapp' => ['icon' => 'fab fa-whatsapp', 'prefix' => 'https://wa.me/', 'title' => 'WhatsApp'],
                    'public_email' => ['icon' => 'fas fa-envelope', 'prefix' => 'mailto:', 'title' => 'Email'],
                ];

                $userSocials = $user['socials'] ?? [];
                $finalOrder = $userSocials['_order'] ?? array_keys($allSocials);

                foreach($finalOrder as $key):
                    $val = $userSocials[$key] ?? null;
                    if(empty($val)) continue;
                    
                    $social = $allSocials[$key] ?? null;
                    if (!$social) continue;

                    if($key === 'whatsapp') $val = preg_replace('/[^0-9]/', '', $val);
                ?>
                    <a href="<?php echo formatSocialUrl($val, $social['prefix']); ?>" target="_blank" title="<?php echo $social['title']; ?>">
                        <i class="<?php echo $social['icon']; ?>"></i>
                    </a>
                <?php endforeach; ?>
            </div>

            <div class="links-list">
                <?php foreach ($links as $link): ?>
                    <a href="<?php echo \App\Core\Config::url('l?id=' . $link['id']); ?>" target="_blank" class="link">
                        <?php if (!empty($link['thumbnail'])): ?>
                            <img src="<?php echo \App\Core\Config::url('public/uploads/links/' . $link['thumbnail']); ?>" class="link-thumb">
                        <?php else: ?>
                            <i class="<?php echo htmlspecialchars($link['icon'] ?: 'fas fa-link'); ?> link-icon"></i>
                        <?php endif; ?>
                        
                        <div class="link-content">
                            <span class="link-title"><?php echo htmlspecialchars($link['title']); ?></span>
                            <?php if (!empty($link['subtitle'])): ?>
                                <span class="link-subtitle"><?php echo htmlspecialchars($link['subtitle']); ?></span>
                            <?php endif; ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>

            <div class="footer-logo">
                POWERED BY <span>ALGRAPHY PRO</span>
            </div>
        </div>
    </div>

    <!-- Matrix Background Script -->
    <script src="<?php echo \App\Core\Config::asset('js/profile.js'); ?>"></script>
</body>
</html>