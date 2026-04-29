<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - AlGraphy Pro Hub</title>
    <link rel="stylesheet" href="<?php echo \App\Core\Config::asset('css/style.css'); ?>">
    <link rel="stylesheet" href="<?php echo \App\Core\Config::asset('css/dashboard.css'); ?>">
    <link rel="stylesheet" href="<?php echo \App\Core\Config::asset('css/modal.css'); ?>">
    <link rel="stylesheet" href="<?php echo \App\Core\Config::asset('css/toast.css'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Favicons -->
    <link rel="icon" type="image/png" href="<?php echo \App\Core\Config::asset('logo/favicon-96x96.png'); ?>" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="<?php echo \App\Core\Config::asset('logo/favicon.svg'); ?>" />
    <link rel="shortcut icon" href="<?php echo \App\Core\Config::asset('logo/favicon.svg'); ?>" />
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo \App\Core\Config::asset('logo/apple-touch-icon.png'); ?>" />
    <link rel="manifest" href="<?php echo \App\Core\Config::asset('logo/site.webmanifest'); ?>" crossorigin="use-credentials" />
</head>

<body class="dashboard-body">
    <div class="dashboard-layout">
        <nav class="navbar">
            <div class="logo">
                <a href="<?php echo \App\Core\Config::url('dashboard'); ?>" class="navbar-logo-link">
                    <?php 
                        $logoPath = \App\Core\Config::root('public/assets/logo/Red_logo_algraphy.svg');
                        $logoData = base64_encode(file_get_contents($logoPath));
                    ?>
                    <img src="data:image/svg+xml;base64,<?php echo $logoData; ?>" alt="AlGraphy"
                        class="navbar-logo-img" fetchpriority="high">
                    <h1 class="navbar-logo-text">Hub</h1>
                </a>
            </div>
            <div class="nav-links">
                <span class="user-welcome">Hi,
                    <strong><?php echo htmlspecialchars($user['username']); ?></strong></span>
                <a href="<?php echo \App\Core\Config::url('settings'); ?>"><i class="fas fa-cog"></i> Settings</a>
                <a href="<?php echo \App\Core\Config::url('logout'); ?>"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </nav>

        <div class="dashboard-container">
            <?php if (isset($_SESSION['success'])): ?>
                <script>window.addEventListener('DOMContentLoaded', () => showToast("<?php echo $_SESSION['success']; ?>", 'success'));</script>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <script>window.addEventListener('DOMContentLoaded', () => showToast("<?php echo $_SESSION['error']; ?>", 'error'));</script>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <div class="dashboard-grid">
                <div class="dashboard-content-main">

                    <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-link"></i></div>
                    <h3>Active Links</h3>
                    <div class="value"><?php echo count(array_filter($links, fn($l) => $l['is_active'])); ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-eye"></i></div>
                    <h3>Profile Views</h3>
                    <div class="value"><?php echo $stats['total_views'] ?? 0; ?></div>
                    <small>(Unique: <?php echo $stats['unique_views'] ?? 0; ?>)</small>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-mouse-pointer"></i></div>
                    <h3>Total Clicks</h3>
                    <div class="value"><?php echo $stats['total_clicks'] ?? 0; ?></div>
                    <small>CTR: <?php
                    $ctr = ($stats['total_views'] > 0) ? round(($stats['total_clicks'] / $stats['total_views']) * 100, 1) : 0;
                    echo $ctr . '%';
                    ?></small>
                </div>
            </div>

            <!-- Share Profile Section -->
            <div class="dashboard-section qr-share-section">
                <div class="qr-share-section-text">
                    <h1>Share Your Profile</h1>
                    <p>Use this QR code on business cards or social media.</p>

                    <div class="mb-20">
                        <a href="<?php echo \App\Core\Config::url($user['username']); ?>" target="_blank"
                            class="qr-preview-link">
                            <i class="fas fa-external-link-alt"></i>
                            View Public Profile</a>
                    </div>

                    <a href="<?php echo \App\Core\Config::url('qr/download'); ?>" class="btn qr-download-btn">
                        <i class="fas fa-download"></i> Download Your QR Code
                    </a>
                </div>
                <img src="<?php echo $qrcode; ?>" alt="Your QR Code" class="qr-code-img">
            </div>

            <!-- Social Media Links Section -->
            <div class="dashboard-section">
                <h2 class="section-title">
                    <i class="fas fa-share-nodes"></i> Social Media Icons (Drag to Reorder)
                </h2>
                <div id="socialGridDashboard" class="social-grid-dashboard">
                    <?php
                    $userSocials = $user['socials'] ?? [];
                    $allSocials = [
                        'instagram' => ['icon' => 'fab fa-instagram', 'label' => 'Instagram'],
                        'x' => ['icon' => 'fab fa-x-twitter', 'label' => 'X (Twitter)'],
                        'linkedin' => ['icon' => 'fab fa-linkedin-in', 'label' => 'LinkedIn'],
                        'whatsapp' => ['icon' => 'fab fa-whatsapp', 'label' => 'WhatsApp'],
                        'tiktok' => ['icon' => 'fab fa-tiktok', 'label' => 'TikTok'],
                        'snapchat' => ['icon' => 'fab fa-snapchat', 'label' => 'Snapchat'],
                        'facebook' => ['icon' => 'fab fa-facebook-f', 'label' => 'Facebook'],
                        'youtube' => ['icon' => 'fab fa-youtube', 'label' => 'YouTube'],
                        'threads' => ['icon' => 'fab fa-threads', 'label' => 'Threads'],
                        'public_email' => ['icon' => 'fas fa-envelope', 'label' => 'Email'],
                    ];

                    $order = $userSocials['_order'] ?? array_keys($allSocials);
                    // Ensure all keys exist in order
                    foreach (array_keys($allSocials) as $key) {
                        if (!in_array($key, $order))
                            $order[] = $key;
                    }

                    // Sort: Actives first, but respect the internal order of each group
                    $actives = [];
                    $inactives = [];
                    foreach ($order as $socialId) {
                        if (isset($allSocials[$socialId])) {
                            $val = $userSocials[$socialId] ?? '';
                            if (!empty($val)) {
                                $actives[] = $socialId;
                            } else {
                                $inactives[] = $socialId;
                            }
                        }
                    }
                    $finalOrder = array_merge($actives, $inactives);

                    foreach ($finalOrder as $socialId):
                        $social = $allSocials[$socialId];
                        $val = $userSocials[$socialId] ?? '';
                        $isSet = !empty($val);
                        ?>
                        <div class="social-icon-item <?php echo $isSet ? 'active' : ''; ?>" draggable="true"
                            data-social-id="<?php echo $socialId; ?>"
                            onclick="if(!isDraggingSocial) openSocialModal('<?php echo $socialId; ?>', '<?php echo $social['label']; ?>', '<?php echo htmlspecialchars($val); ?>', '<?php echo $social['icon']; ?>')"
                            style="background: <?php echo $isSet ? 'var(--primary-color)' : 'rgba(255,255,255,0.05)'; ?>;"
                            title="<?php echo $social['label']; ?>">
                            <i class="<?php echo $social['icon']; ?>"
                                style="color: <?php echo $isSet ? '#fff' : 'var(--text-muted)'; ?>;"></i>
                            <?php if ($isSet): ?>
                                <span class="social-active-dot"></span>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="links-header">
                <h2>Manage Your Links</h2>
                <button class="btn add-link-btn" id="addLinkBtn">
                    <i class="fas fa-plus"></i> Add New Link
                </button>
            </div>

            <ul id="linksList">
                <?php if (empty($links)): ?>
                    <div class="empty-state">
                        <i class="fas fa-link empty-state-icon"></i>
                        <p>No links added yet. Click the button above to start building your profile!</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($links as $link): ?>
                        <li class="link-item" data-id="<?php echo $link['id']; ?>" draggable="true">
                            <div class="drag-handle">
                                <i class="fas fa-grip-vertical"></i>
                            </div>
                            <div class="link-icon">
                                <i class="<?php echo htmlspecialchars($link['icon'] ?: 'fas fa-link'); ?>"></i>
                            </div>
                            <div class="link-info">
                                <span class="link-title"><?php echo htmlspecialchars($link['title']); ?></span>
                                <?php if (!empty($link['subtitle'])): ?>
                                    <span class="link-subtitle-text">
                                        <?php echo htmlspecialchars($link['subtitle']); ?>
                                    </span>
                                <?php endif; ?>
                                <span class="link-url"><?php echo htmlspecialchars($link['url']); ?></span>
                            </div>
                            <div class="link-actions">
                                <label class="switch">
                                    <input type="checkbox" class="toggle-link" data-id="<?php echo $link['id']; ?>" <?php echo $link['is_active'] ? 'checked' : ''; ?>>
                                    <span class="slider"></span>
                                </label>
                                <button class="action-btn edit-btn" data-id="<?php echo $link['id']; ?>"
                                    data-title="<?php echo htmlspecialchars($link['title']); ?>"
                                    data-subtitle="<?php echo htmlspecialchars($link['subtitle'] ?? ''); ?>"
                                    data-url="<?php echo htmlspecialchars($link['url']); ?>"
                                    data-icon="<?php echo htmlspecialchars($link['icon'] ?: 'fas fa-link'); ?>">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="<?php echo \App\Core\Config::url('link/delete?id=' . $link['id']); ?>"
                                    class="action-btn delete"
                                    onclick="return confirm('Are you sure you want to delete this link?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div> <!-- End dashboard-content-main -->

                <div class="dashboard-preview-sidebar">
                    <div class="preview-sticky">
                        <div class="phone-mockup">
                            <div class="phone-frame">
                                <div class="phone-buttons">
                                    <div class="btn-silent"></div>
                                    <div class="btn-vol-up"></div>
                                    <div class="btn-vol-down"></div>
                                    <div class="btn-power"></div>
                                </div>
                                <div class="phone-screen">
                                    <div class="dynamic-island-wrapper">
                                        <div class="dynamic-island"></div>
                                    </div>
                                    <iframe src="<?php echo \App\Core\Config::url($user['username']); ?>" frameborder="0" id="profilePreview"></iframe>
                                </div>
                            </div>
                        </div>
                        <p class="preview-hint"><i class="fas fa-sync-alt"></i> Live Preview</p>
                    </div>
                </div>
            </div> <!-- End dashboard-grid -->
        </div> <!-- End dashboard-container -->
    </div>

    <!-- Add/Edit Link Modal -->
    <div id="addLinkModal" class="modal">
        <div class="modal-content">
            <h3 id="modalTitle" class="modal-title">Add New Link</h3>
            <form id="linkForm" action="<?php echo \App\Core\Config::url('link/add'); ?>" method="POST">
                <input type="hidden" id="linkId" name="id" value="">

                <div class="form-group">
                    <label for="title">Link Title</label>
                    <input type="text" id="title" name="title" placeholder="e.g. My Instagram" required>
                </div>
                <div class="form-group">
                    <label for="subtitle">Subtitle (Optional)</label>
                    <input type="text" id="subtitle" name="subtitle" placeholder="e.g. Follow for daily updates">
                </div>
                <div class="form-group">
                    <label for="url">URL, Phone, or Email</label>
                    <input type="text" id="url" name="url" placeholder="https://... , +966... , or hello@example.com"
                        required>
                    <small style="color: var(--text-muted); font-size: 0.75rem;">Enter a website link, phone number, or
                        email address.</small>
                </div>


                <div class="form-group">
                    <label>Link Icon</label>
                    <div class="icon-picker-grid">
                        <!-- Socials -->
                        <div class="icon-option selected" data-icon="fas fa-link" title="Default"><i
                                class="fas fa-link"></i></div>
                        <div class="icon-option" data-icon="fab fa-instagram" title="Instagram"><i
                                class="fab fa-instagram"></i></div>
                        <div class="icon-option" data-icon="fab fa-x-twitter" title="X / Twitter"><i
                                class="fab fa-x-twitter"></i></div>
                        <div class="icon-option" data-icon="fab fa-tiktok" title="TikTok"><i class="fab fa-tiktok"></i>
                        </div>
                        <div class="icon-option" data-icon="fab fa-youtube" title="YouTube"><i
                                class="fab fa-youtube"></i></div>
                        <div class="icon-option" data-icon="fab fa-facebook" title="Facebook"><i
                                class="fab fa-facebook"></i></div>
                        <div class="icon-option" data-icon="fab fa-whatsapp" title="WhatsApp"><i
                                class="fab fa-whatsapp"></i></div>
                        <div class="icon-option" data-icon="fab fa-snapchat" title="Snapchat"><i
                                class="fab fa-snapchat"></i></div>
                        <div class="icon-option" data-icon="fab fa-linkedin" title="LinkedIn"><i
                                class="fab fa-linkedin"></i></div>
                        <div class="icon-option" data-icon="fab fa-threads" title="Threads"><i
                                class="fab fa-threads"></i></div>
                        <div class="icon-option" data-icon="fab fa-telegram" title="Telegram"><i
                                class="fab fa-telegram"></i></div>
                        <div class="icon-option" data-icon="fab fa-discord" title="Discord"><i
                                class="fab fa-discord"></i></div>
                        <!-- Music & Video -->
                        <div class="icon-option" data-icon="fab fa-spotify" title="Spotify"><i
                                class="fab fa-spotify"></i></div>
                        <div class="icon-option" data-icon="fab fa-apple" title="Apple Music/App Store"><i
                                class="fab fa-apple"></i></div>
                        <div class="icon-option" data-icon="fab fa-soundcloud" title="SoundCloud"><i
                                class="fab fa-soundcloud"></i></div>
                        <div class="icon-option" data-icon="fab fa-twitch" title="Twitch"><i class="fab fa-twitch"></i>
                        </div>
                        <div class="icon-option" data-icon="fas fa-music" title="Music"><i class="fas fa-music"></i>
                        </div>
                        <div class="icon-option" data-icon="fas fa-play" title="Play"><i class="fas fa-play"></i></div>
                        <!-- Utility -->
                        <div class="icon-option" data-icon="fas fa-envelope" title="Email"><i
                                class="fas fa-envelope"></i></div>
                        <div class="icon-option" data-icon="fas fa-globe" title="Website"><i class="fas fa-globe"></i>
                        </div>
                        <div class="icon-option" data-icon="fas fa-phone" title="Phone"><i class="fas fa-phone"></i>
                        </div>
                        <div class="icon-option" data-icon="fas fa-map-marker-alt" title="Location"><i
                                class="fas fa-map-marker-alt"></i></div>
                        <div class="icon-option" data-icon="fas fa-shopping-cart" title="Shop"><i
                                class="fas fa-shopping-cart"></i></div>
                        <div class="icon-option" data-icon="fas fa-file-pdf" title="PDF/File"><i
                                class="fas fa-file-pdf"></i></div>
                        <!-- Gaming & Tech -->
                        <div class="icon-option" data-icon="fab fa-github" title="GitHub"><i class="fab fa-github"></i>
                        </div>
                        <div class="icon-option" data-icon="fab fa-steam" title="Steam"><i class="fab fa-steam"></i>
                        </div>
                        <div class="icon-option" data-icon="fab fa-playstation" title="PlayStation"><i
                                class="fab fa-playstation"></i></div>
                        <div class="icon-option" data-icon="fab fa-xbox" title="Xbox"><i class="fab fa-xbox"></i></div>
                        <div class="icon-option" data-icon="fas fa-gamepad" title="Gaming"><i
                                class="fas fa-gamepad"></i></div>
                        <div class="icon-option" data-icon="fas fa-code" title="Code"><i class="fas fa-code"></i></div>
                        <!-- Payments -->
                        <div class="icon-option" data-icon="fab fa-paypal" title="PayPal"><i class="fab fa-paypal"></i>
                        </div>
                        <div class="icon-option" data-icon="fas fa-credit-card" title="Credit Card"><i
                                class="fas fa-credit-card"></i></div>
                        <div class="icon-option" data-icon="fab fa-bitcoin" title="Crypto"><i
                                class="fab fa-bitcoin"></i></div>
                        <div class="icon-option" data-icon="fas fa-coffee" title="Buy Me a Coffee"><i
                                class="fas fa-coffee"></i></div>
                        <div class="icon-option" data-icon="fas fa-heart" title="Donation"><i class="fas fa-heart"></i>
                        </div>
                        <div class="icon-option" data-icon="fas fa-star" title="Rating"><i class="fas fa-star"></i>
                        </div>
                    </div>
                    <input type="hidden" id="selectedIcon" name="icon" value="fas fa-link">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" id="closeModal"
                        style="background:var(--gray); width:auto; padding:8px 20px;">Cancel</button>
                    <button type="submit" class="btn" style="width:auto; padding:8px 20px;">Save Link</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Social Media Modal -->
    <div id="socialModal" class="modal"
        style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); align-items: center; justify-content: center;">
        <div class="modal-content"
            style="padding: 30px; border-radius: 15px; width: 90%; max-width: 400px; border: 1px solid rgba(220, 39, 38, 0.3);">
            <div style="text-align: center; margin-bottom: 20px;">
                <div id="socialIconContainer"
                    style="width: 60px; height: 60px; background: var(--primary-color); border-radius: 15px; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;">
                    <i id="socialIconDisplay" class="fab fa-instagram" style="font-size: 2rem; color: #fff;"></i>
                </div>
                <h2 id="socialModalTitle" style="font-size: 1.4rem;">Edit Instagram</h2>
            </div>

            <form id="socialForm">
                <input type="hidden" id="socialKey" name="key">
                <div class="form-group" style="margin-bottom: 20px;">
                    <label id="socialLabel"
                        style="display: block; margin-bottom: 8px; color: var(--text-muted); font-size: 0.9rem;">Username
                        or URL</label>
                    <input type="text" id="socialValue" name="value"
                        style="width: 100%; padding: 12px; background: #111; color: #fff; border: 1px solid rgba(255,255,255,0.1); border-radius: 8px;"
                        placeholder="Enter here...">
                </div>
                <div style="display: flex; gap: 10px;">
                    <button type="button" onclick="closeSocialModal()" class="btn"
                        style="background: var(--gray); flex: 1;">Cancel</button>
                    <button type="submit" class="btn" style="flex: 2;">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Pass dynamic PHP URLs to the external JavaScript file
        window.AppConfig = {
            urls: {
                updateSocial: '<?php echo \App\Core\Config::url('user/update-social'); ?>',
                addLink: '<?php echo \App\Core\Config::url('link/add'); ?>',
                updateLink: '<?php echo \App\Core\Config::url('link/update'); ?>',
                toggleLink: '<?php echo \App\Core\Config::url('link/toggle'); ?>',
                reorderSocials: '<?php echo \App\Core\Config::url('user/reorder-socials'); ?>',
                reorderLinks: '<?php echo \App\Core\Config::url('link/reorder'); ?>',
                uploadPath: '<?php echo \App\Core\Config::upload('links/'); ?>'
            }
        };
    </script>
    <script src="<?php echo \App\Core\Config::asset('js/toast.js'); ?>"></script>
    <script src="<?php echo \App\Core\Config::asset('js/dashboard.js'); ?>"></script>
</body>

</html>