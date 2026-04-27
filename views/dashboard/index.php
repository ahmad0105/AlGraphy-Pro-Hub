<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - AlGraphy Pro Hub</title>
    <link rel="stylesheet" href="<?php echo \App\Core\Config::asset('css/style.css'); ?>">
    <link rel="stylesheet" href="<?php echo \App\Core\Config::asset('css/dashboard.css'); ?>">
    <link rel="stylesheet" href="<?php echo \App\Core\Config::asset('css/modal.css'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .link-item.dragging {
            opacity: 0.5;
            background: rgba(220, 39, 38, 0.1);
            border: 1px dashed var(--primary-color);
        }
    </style>
</head>

<body class="dashboard-body">
    <div class="dashboard-layout">
        <nav class="navbar">
            <div class="logo">
                <a href="<?php echo \App\Core\Config::url('dashboard'); ?>"
                    style="text-decoration: none; color: inherit;">
                    <h1>AlGraphy <span>Pro Hub</span></h1>
                </a>
            </div>
            <div class="nav-links">
                <span class="user-welcome">Hi,
                    <strong><?php echo htmlspecialchars($user['display_name'] ?: $user['username']); ?></strong></span>
                <a href="<?php echo \App\Core\Config::url('settings'); ?>"><i class="fas fa-cog"></i> Settings</a>
                <a href="<?php echo \App\Core\Config::url('logout'); ?>"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </nav>

        <div class="dashboard-container">
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($_SESSION['success']);
                unset($_SESSION['success']); ?></div>
            <?php endif; ?>
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($_SESSION['error']);
                unset($_SESSION['error']); ?></div>
            <?php endif; ?>

            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Active Links</h3>
                    <div class="value"><?php echo count(array_filter($links, fn($l) => $l['is_active'])); ?></div>
                </div>
                <div class="stat-card">
                    <h3>Profile Views</h3>
                    <div class="value"><?php echo $stats['total_views'] ?? 0; ?></div>
                    <small style="color: var(--text-muted); font-size: 0.7rem;">(Unique:
                        <?php echo $stats['unique_views'] ?? 0; ?>)</small>
                </div>
                <div class="stat-card">
                    <h3>Total Clicks</h3>
                    <div class="value"><?php echo $stats['total_clicks'] ?? 0; ?></div>
                    <small style="color: var(--text-muted); font-size: 0.7rem;">CTR: <?php
                    $ctr = ($stats['total_views'] > 0) ? round(($stats['total_clicks'] / $stats['total_views']) * 100, 1) : 0;
                    echo $ctr . '%';
                    ?></small>
                </div>
            </div>

            <!-- Share Profile Section -->
            <div class="dashboard-section qr-share-section"
                style="background: var(--darker); border-radius: 12px; padding: 20px; margin-bottom: 30px; display: flex; align-items: center; justify-content: space-between; border: 1px solid rgba(255,255,255,0.05);">
                <div>
                    <h1 style="font-size: 1.2rem; margin-bottom: 5px; text-align: left;">Share Your Profile</h1>
                    <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 15px;">Use this QR code on
                        business cards or social media.</p>

                    <a href="<?php echo \App\Core\Config::url('qr/download'); ?>" class="btn"
                        style="width: auto; padding: 10px 20px; display: inline-block; text-decoration: none;">
                        <i class="fas fa-download"></i> Download HD PNG
                    </a>
                </div>
                <img src="<?php echo $qrcode; ?>" alt="Your QR Code"
                    style="width: 120px; height: 120px; display: block; background: white;padding: 4px;border-radius: 8px;">
            </div>

            <!-- Social Media Links Section -->
            <div class="dashboard-section"
                style="background: var(--darker); border-radius: 12px; padding: 25px; margin-bottom: 30px; border: 1px solid rgba(255,255,255,0.05);">
                <h2 style="font-size: 1.2rem; margin-bottom: 20px; text-align: left;"><i class="fas fa-share-nodes"
                        style="color: var(--primary-color); margin-right: 10px;"></i> Social Media Icons (Drag to
                    Reorder)</h2>
                <div id="socialGridDashboard"
                    style="display: flex; gap: 15px; flex-wrap: wrap; justify-content: center; padding: 20px; background: rgba(0,0,0,0.2); border-radius: 15px; border: 1px solid rgba(255,255,255,0.05); min-height: 90px;">
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
                        if (!in_array($key, $order)) $order[] = $key;
                    }

                    // Sort: Actives first
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
                            style="cursor: grab; background: <?php echo $isSet ? 'var(--primary-color)' : 'rgba(255,255,255,0.05)'; ?>; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; border-radius: 10px; transition: all 0.3s ease; position: relative;"
                            title="<?php echo $social['label']; ?>">
                            <i class="<?php echo $social['icon']; ?>"
                                style="font-size: 1.4rem; color: <?php echo $isSet ? '#fff' : 'var(--text-muted)'; ?>;"></i>
                            <?php if ($isSet): ?>
                                <span
                                    style="position: absolute; top: -5px; right: -5px; background: #10b981; width: 15px; height: 15px; border-radius: 50%; border: 2px solid var(--darker);"></span>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="links-header">
                <h2>Manage Your Links</h2>
                <button class="btn" id="addLinkBtn" style="width: auto; padding: 10px 25px;">
                    <i class="fas fa-plus"></i> Add New Link
                </button>
            </div>

            <ul id="linksList">
                <?php if (empty($links)): ?>
                    <div class="empty-state">
                        <i class="fas fa-link"
                            style="font-size: 3rem; color: var(--gray); margin-bottom: 20px; display: block;"></i>
                        <p>No links added yet. Click the button above to start building your profile!</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($links as $link): ?>
                        <li class="link-item" data-id="<?php echo $link['id']; ?>" draggable="true">
                            <div class="drag-handle">
                                <i class="fas fa-grip-vertical"></i>
                            </div>
                            <div class="link-icon">
                                <?php if (!empty($link['thumbnail'])): ?>
                                    <img src="<?php echo \App\Core\Config::url('public/uploads/links/' . $link['thumbnail']); ?>" 
                                         style="width: 40px; height: 40px; border-radius: 8px; object-fit: cover;">
                                <?php else: ?>
                                    <i class="<?php echo htmlspecialchars($link['icon'] ?: 'fas fa-link'); ?>"></i>
                                <?php endif; ?>
                            </div>
                            <div class="link-info">
                                <span class="link-title"><?php echo htmlspecialchars($link['title']); ?></span>
                                <?php if (!empty($link['subtitle'])): ?>
                                    <span class="link-url"
                                        style="color: var(--text-color); font-size: 0.75rem; opacity: 0.8; margin-bottom: 2px; display: block;">
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
                                    data-icon="<?php echo htmlspecialchars($link['icon'] ?: 'fas fa-link'); ?>"
                                    data-thumbnail="<?php echo htmlspecialchars($link['thumbnail'] ?? ''); ?>">
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
            </ul>
        </div>
    </div>

    <!-- Add/Edit Link Modal -->
    <div id="addLinkModal" class="modal">
        <div class="modal-content">
            <h3 id="modalTitle" style="margin-bottom:20px;">Add New Link</h3>
            <form id="linkForm" action="<?php echo \App\Core\Config::url('link/add'); ?>" method="POST" enctype="multipart/form-data">
                <input type="hidden" id="linkId" name="id" value="">
                <input type="hidden" id="currentThumbnail" name="current_thumbnail" value="">
                
                <div class="form-group">
                    <label for="title">Link Title</label>
                    <input type="text" id="title" name="title" placeholder="e.g. My Instagram" required>
                </div>
                <div class="form-group">
                    <label for="subtitle">Subtitle (Optional)</label>
                    <input type="text" id="subtitle" name="subtitle" placeholder="e.g. Follow for daily updates">
                </div>
                <div class="form-group">
                    <label for="url">URL</label>
                    <input type="url" id="url" name="url" placeholder="https://instagram.com/..." required>
                    <small style="color: var(--text-muted); font-size: 0.75rem;">Icon will be detected automatically based on the URL.</small>
                </div>
                <div class="form-group">
                    <label>Thumbnail / Custom Image (Optional)</label>
                    <div style="display: flex; align-items: center; gap: 15px; background: rgba(255,255,255,0.05); padding: 15px; border-radius: 10px; border: 1px dashed rgba(255,255,255,0.1);">
                        <div id="thumbPreview" style="width: 50px; height: 50px; background: rgba(0,0,0,0.3); border-radius: 8px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                            <i class="fas fa-image" style="color: var(--text-muted);"></i>
                        </div>
                        <div style="flex: 1;">
                            <input type="file" name="thumbnail" accept="image/*" style="font-size: 0.85rem;" onchange="previewThumbnail(this)">
                            <p style="font-size: 0.7rem; color: var(--text-muted); margin-top: 5px;">Overrides any icon below.</p>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Link Icon</label>
                    <div class="icon-picker-grid" style="display: grid; grid-template-columns: repeat(6, 1fr); gap: 10px; max-height: 200px; overflow-y: auto; background: rgba(0,0,0,0.2); padding: 10px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.05);">
                        <!-- Socials -->
                        <div class="icon-option selected" data-icon="fas fa-link" title="Default"><i class="fas fa-link"></i></div>
                        <div class="icon-option" data-icon="fab fa-instagram" title="Instagram"><i class="fab fa-instagram"></i></div>
                        <div class="icon-option" data-icon="fab fa-x-twitter" title="X / Twitter"><i class="fab fa-x-twitter"></i></div>
                        <div class="icon-option" data-icon="fab fa-tiktok" title="TikTok"><i class="fab fa-tiktok"></i></div>
                        <div class="icon-option" data-icon="fab fa-youtube" title="YouTube"><i class="fab fa-youtube"></i></div>
                        <div class="icon-option" data-icon="fab fa-facebook" title="Facebook"><i class="fab fa-facebook"></i></div>
                        <div class="icon-option" data-icon="fab fa-whatsapp" title="WhatsApp"><i class="fab fa-whatsapp"></i></div>
                        <div class="icon-option" data-icon="fab fa-snapchat" title="Snapchat"><i class="fab fa-snapchat"></i></div>
                        <div class="icon-option" data-icon="fab fa-linkedin" title="LinkedIn"><i class="fab fa-linkedin"></i></div>
                        <div class="icon-option" data-icon="fab fa-threads" title="Threads"><i class="fab fa-threads"></i></div>
                        <div class="icon-option" data-icon="fab fa-telegram" title="Telegram"><i class="fab fa-telegram"></i></div>
                        <div class="icon-option" data-icon="fab fa-discord" title="Discord"><i class="fab fa-discord"></i></div>
                        <!-- Music & Video -->
                        <div class="icon-option" data-icon="fab fa-spotify" title="Spotify"><i class="fab fa-spotify"></i></div>
                        <div class="icon-option" data-icon="fab fa-apple" title="Apple Music/App Store"><i class="fab fa-apple"></i></div>
                        <div class="icon-option" data-icon="fab fa-soundcloud" title="SoundCloud"><i class="fab fa-soundcloud"></i></div>
                        <div class="icon-option" data-icon="fab fa-twitch" title="Twitch"><i class="fab fa-twitch"></i></div>
                        <div class="icon-option" data-icon="fas fa-music" title="Music"><i class="fas fa-music"></i></div>
                        <div class="icon-option" data-icon="fas fa-play" title="Play"><i class="fas fa-play"></i></div>
                        <!-- Utility -->
                        <div class="icon-option" data-icon="fas fa-envelope" title="Email"><i class="fas fa-envelope"></i></div>
                        <div class="icon-option" data-icon="fas fa-globe" title="Website"><i class="fas fa-globe"></i></div>
                        <div class="icon-option" data-icon="fas fa-phone" title="Phone"><i class="fas fa-phone"></i></div>
                        <div class="icon-option" data-icon="fas fa-map-marker-alt" title="Location"><i class="fas fa-map-marker-alt"></i></div>
                        <div class="icon-option" data-icon="fas fa-shopping-cart" title="Shop"><i class="fas fa-shopping-cart"></i></div>
                        <div class="icon-option" data-icon="fas fa-file-pdf" title="PDF/File"><i class="fas fa-file-pdf"></i></div>
                        <!-- Gaming & Tech -->
                        <div class="icon-option" data-icon="fab fa-github" title="GitHub"><i class="fab fa-github"></i></div>
                        <div class="icon-option" data-icon="fab fa-steam" title="Steam"><i class="fab fa-steam"></i></div>
                        <div class="icon-option" data-icon="fab fa-playstation" title="PlayStation"><i class="fab fa-playstation"></i></div>
                        <div class="icon-option" data-icon="fab fa-xbox" title="Xbox"><i class="fab fa-xbox"></i></div>
                        <div class="icon-option" data-icon="fas fa-gamepad" title="Gaming"><i class="fas fa-gamepad"></i></div>
                        <div class="icon-option" data-icon="fas fa-code" title="Code"><i class="fas fa-code"></i></div>
                        <!-- Payments -->
                        <div class="icon-option" data-icon="fab fa-paypal" title="PayPal"><i class="fab fa-paypal"></i></div>
                        <div class="icon-option" data-icon="fas fa-credit-card" title="Credit Card"><i class="fas fa-credit-card"></i></div>
                        <div class="icon-option" data-icon="fab fa-bitcoin" title="Crypto"><i class="fab fa-bitcoin"></i></div>
                        <div class="icon-option" data-icon="fas fa-coffee" title="Buy Me a Coffee"><i class="fas fa-coffee"></i></div>
                        <div class="icon-option" data-icon="fas fa-heart" title="Donation"><i class="fas fa-heart"></i></div>
                        <div class="icon-option" data-icon="fas fa-star" title="Rating"><i class="fas fa-star"></i></div>
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
        let isDraggingSocial = false;
        // Social Modal Logic
        const socialModal = document.getElementById('socialModal');
        const socialForm = document.getElementById('socialForm');

        function openSocialModal(key, label, value, icon) {
            if (key === 'twitter') key = 'x';
            document.getElementById('socialKey').value = key;
            document.getElementById('socialModalTitle').innerText = 'Edit ' + label;
            document.getElementById('socialLabel').innerText = label + (key === 'public_email' ? ' Email' : ' Username/URL');
            document.getElementById('socialValue').value = value;
            document.getElementById('socialIconDisplay').className = icon;
            socialModal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function closeSocialModal() {
            socialModal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        socialForm.onsubmit = function (e) {
            e.preventDefault();
            const key = document.getElementById('socialKey').value;
            const value = document.getElementById('socialValue').value;

            const formData = new FormData();
            formData.append('key', key);
            formData.append('value', value);

            fetch('<?php echo \App\Core\Config::url('user/update-social'); ?>', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload(); // Quickest way to refresh the grid icons
                    } else {
                        alert('Error: ' + data.message);
                        document.body.style.overflow = 'auto';
                    }
                });
        };

        // Modal Logic
        const modal = document.getElementById('addLinkModal');
        const btn = document.getElementById('addLinkBtn');
        const close = document.getElementById('closeModal');
        const iconOptions = document.querySelectorAll('.icon-option');
        const selectedIconInput = document.getElementById('selectedIcon');

        btn.onclick = () => {
            document.getElementById('modalTitle').innerText = 'Add New Link';
            document.getElementById('linkForm').action = '<?php echo \App\Core\Config::url('link/add'); ?>';
            document.getElementById('linkId').value = '';
            document.getElementById('currentThumbnail').value = '';
            document.getElementById('title').value = '';
            document.getElementById('subtitle').value = '';
            document.getElementById('url').value = '';
            document.getElementById('thumbPreview').innerHTML = '<i class="fas fa-image" style="color: var(--text-muted);"></i>';
            document.getElementById('linkForm').querySelector('input[type="file"]').value = '';
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        };

        function previewThumbnail(input) {
            const preview = document.getElementById('thumbPreview');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    preview.innerHTML = `<img src="${e.target.result}" style="width:100%; height:100%; object-fit:cover;">`;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        const editBtns = document.querySelectorAll('.edit-btn');
        editBtns.forEach(editBtn => {
            editBtn.onclick = function () {
                const id = this.getAttribute('data-id');
                const title = this.getAttribute('data-title');
                const subtitle = this.getAttribute('data-subtitle');
                const url = this.getAttribute('data-url');
                const thumbnail = this.getAttribute('data-thumbnail');
                const icon = this.getAttribute('data-icon');

                document.getElementById('modalTitle').innerText = 'Edit Link';
                document.getElementById('linkForm').action = '<?php echo \App\Core\Config::url('link/update'); ?>';
                document.getElementById('linkId').value = id;
                document.getElementById('currentThumbnail').value = thumbnail;
                document.getElementById('title').value = title;
                document.getElementById('subtitle').value = subtitle;
                document.getElementById('url').value = url;
                document.getElementById('linkForm').querySelector('input[type="file"]').value = '';

                // Handle icon selection in grid
                iconOptions.forEach(opt => opt.classList.remove('selected'));
                const matchedIcon = document.querySelector(`.icon-option[data-icon="${icon}"]`) || document.querySelector('.icon-option[data-icon="fas fa-link"]');
                if (matchedIcon) {
                    matchedIcon.classList.add('selected');
                    selectedIconInput.value = matchedIcon.getAttribute('data-icon');
                    matchedIcon.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }

                const preview = document.getElementById('thumbPreview');
                if (thumbnail) {
                    preview.innerHTML = `<img src="<?php echo \App\Core\Config::url('public/uploads/links/'); ?>${thumbnail}" style="width:100%; height:100%; object-fit:cover;">`;
                } else {
                    preview.innerHTML = '<i class="fas fa-image" style="color: var(--text-muted);"></i>';
                }

                modal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            };
        });

        close.onclick = () => {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        };
        window.onclick = (e) => {
            if (e.target == modal) {
                modal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
            if (e.target == socialModal) {
                socialModal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        }

        // Manual Icon Selection Logic
        iconOptions.forEach(option => {
            option.addEventListener('click', function () {
                iconOptions.forEach(opt => opt.classList.remove('selected'));
                this.classList.add('selected');
                selectedIconInput.value = this.getAttribute('data-icon');
            });
        });

        // Smart Icon Detection on URL input
        const urlInput = document.getElementById('url');
        urlInput.addEventListener('input', function() {
            const url = this.value.toLowerCase();
            const iconMappings = {
                'instagram.com': 'fab fa-instagram',
                'twitter.com': 'fab fa-x-twitter',
                'x.com': 'fab fa-x-twitter',
                'tiktok.com': 'fab fa-tiktok',
                'youtube.com': 'fab fa-youtube',
                'youtu.be': 'fab fa-youtube',
                'facebook.com': 'fab fa-facebook',
                'fb.com': 'fab fa-facebook',
                'whatsapp.com': 'fab fa-whatsapp',
                'wa.me': 'fab fa-whatsapp',
                'snapchat.com': 'fab fa-snapchat',
                'linkedin.com': 'fab fa-linkedin',
                'threads.net': 'fab fa-threads',
                'telegram.me': 'fab fa-telegram',
                't.me': 'fab fa-telegram',
                'discord.gg': 'fab fa-discord',
                'spotify.com': 'fab fa-spotify',
                'apple.com': 'fab fa-apple',
                'github.com': 'fab fa-github',
                'paypal.me': 'fab fa-paypal',
                'twitch.tv': 'fab fa-twitch',
                'soundcloud.com': 'fab fa-soundcloud'
            };

            for (const [domain, icon] of Object.entries(iconMappings)) {
                if (url.includes(domain)) {
                    // Update the hidden input value directly (works even if icon is not in the grid)
                    selectedIconInput.value = icon;

                    // If the icon IS in the grid, highlight it and scroll to it
                    const option = document.querySelector(`.icon-option[data-icon="${icon}"]`);
                    iconOptions.forEach(opt => opt.classList.remove('selected'));
                    if (option) {
                        option.classList.add('selected');
                        option.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    }
                    break;
                }
            }
        });

        const toggles = document.querySelectorAll('.toggle-link');
        toggles.forEach(toggle => {
            toggle.addEventListener('change', function () {
                const id = this.getAttribute('data-id');
                const formData = new FormData();
                formData.append('id', id);

                fetch('<?php echo \App\Core\Config::url('link/toggle'); ?>', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (!data.success) {
                            alert('Failed to update status.');
                            this.checked = !this.checked;
                        }
                    });
            });
        });

        // --- OOP Sortable Manager Class ---
        class SortableManager {
            constructor(config) {
                this.container = document.getElementById(config.containerId);
                this.itemSelector = config.itemSelector;
                this.onOrderChange = config.onOrderChange;
                this.orientation = config.orientation || 'vertical';
                this.draggingElement = null;
                this.onDragStateChange = config.onDragStateChange || null;

                if (this.container) this.init();
            }

            init() {
                // --- Desktop Mouse Drag Events ---
                this.container.addEventListener('dragstart', (e) => {
                    const item = e.target.closest(this.itemSelector);
                    if (item) {
                        this.draggingElement = item;
                        if (this.onDragStateChange) this.onDragStateChange(true);
                        setTimeout(() => item.classList.add('dragging'), 0);
                    }
                });

                this.container.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    if (!this.draggingElement) return;

                    const afterElement = this.getDragAfterElement(e.clientX, e.clientY);
                    if (afterElement == null) {
                        this.container.appendChild(this.draggingElement);
                    } else {
                        this.container.insertBefore(this.draggingElement, afterElement);
                    }
                });

                this.container.addEventListener('dragend', (e) => {
                    this.endDrag();
                });

                // --- Mobile Touch Events ---
                this.container.addEventListener('touchstart', (e) => {
                    const item = e.target.closest(this.itemSelector);
                    if (item) {
                        this.draggingElement = item;
                        if (this.onDragStateChange) this.onDragStateChange(true);
                        item.classList.add('dragging');
                    }
                }, { passive: true });

                this.container.addEventListener('touchmove', (e) => {
                    if (!this.draggingElement) return;
                    
                    const touch = e.touches[0];
                    const afterElement = this.getDragAfterElement(touch.clientX, touch.clientY);
                    
                    if (afterElement == null) {
                        this.container.appendChild(this.draggingElement);
                    } else {
                        this.container.insertBefore(this.draggingElement, afterElement);
                    }
                    
                    if (e.cancelable) e.preventDefault();
                }, { passive: false });

                this.container.addEventListener('touchend', (e) => {
                    this.endDrag();
                });
            }

            endDrag() {
                if (this.draggingElement) {
                    this.draggingElement.classList.remove('dragging');
                    this.onOrderChange(this.getCurrentOrder());
                    setTimeout(() => {
                        if (this.onDragStateChange) this.onDragStateChange(false);
                    }, 100);
                    this.draggingElement = null;
                }
            }

            getDragAfterElement(x, y) {
                const draggableElements = [...this.container.querySelectorAll(`${this.itemSelector}:not(.dragging)`)];
                return draggableElements.reduce((closest, child) => {
                    const box = child.getBoundingClientRect();
                    const offset = (this.orientation === 'horizontal')
                        ? x - box.left - box.width / 2
                        : y - box.top - box.height / 2;

                    if (offset < 0 && offset > closest.offset) {
                        return { offset: offset, element: child };
                    } else {
                        return closest;
                    }
                }, { offset: Number.NEGATIVE_INFINITY }).element;
            }

            getCurrentOrder() {
                return [...this.container.querySelectorAll(this.itemSelector)].map(item =>
                    item.getAttribute('data-id') || item.getAttribute('data-social-id')
                );
            }
        }

        // --- Initialize Social Icons Sortable ---
        new SortableManager({
            containerId: 'socialGridDashboard',
            itemSelector: '.social-icon-item',
            orientation: 'horizontal',
            onDragStateChange: (state) => { isDraggingSocial = state; },
            onOrderChange: (order) => {
                const formData = new FormData();
                formData.append('order', JSON.stringify(order));
                fetch('<?php echo \App\Core\Config::url('user/reorder-socials'); ?>', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (!data.success) console.error('Failed to save social order');
                    });
            }
        });

        // --- Initialize Links Sortable ---
        new SortableManager({
            containerId: 'linksList',
            itemSelector: '.link-item',
            orientation: 'vertical',
            onOrderChange: (order) => {
                fetch('<?php echo \App\Core\Config::url('link/reorder'); ?>', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ order: order })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (!data.success) console.error('Failed to save link order');
                    });
            }
        });
    </script>
</body>

</html>