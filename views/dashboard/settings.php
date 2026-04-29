<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Settings - AlGraphy Pro Hub</title>
    <link rel="stylesheet" href="<?php echo \App\Core\Config::asset('css/style.css'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Cropper.js for image cropping (Local) -->
    <link rel="stylesheet" href="<?php echo \App\Core\Config::asset('css/dashboard.css'); ?>">
    <link rel="stylesheet" href="<?php echo \App\Core\Config::asset('css/settings.css'); ?>">
    <link rel="stylesheet" href="<?php echo \App\Core\Config::asset('css/modal.css'); ?>">
    <link rel="stylesheet" href="<?php echo \App\Core\Config::asset('css/toast.css'); ?>">
    <link rel="stylesheet" href="<?php echo \App\Core\Config::asset('css/cropper.min.css'); ?>">
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
                    <img src="<?php echo \App\Core\Config::asset('logo/Red_logo_algraphy.svg'); ?>" alt="AlGraphy"
                        class="navbar-logo-img">
                    <h1 class="navbar-logo-text">Hub</h1>
                </a>
            </div>
            <div class="nav-links">
                <span class="user-welcome">Hi,
                    <strong><?php echo htmlspecialchars($user['username']); ?></strong></span>
                <a href="<?php echo \App\Core\Config::url('dashboard'); ?>"><i class="fas fa-home"></i> Dashboard</a>
                <a href="<?php echo \App\Core\Config::url('logout'); ?>"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </nav>

        <div class="dashboard-card">
            <div class="card card-transparent">

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

                <!-- ═══ Tabs Navigation ═══ -->
                <div class="settings-tabs">
                    <button class="settings-tab active" onclick="switchTab('profile')">
                        <i class="fas fa-user"></i>
                        <span>Profile</span>
                    </button>
                    <button class="settings-tab" onclick="switchTab('appearance')">
                        <i class="fas fa-palette"></i>
                        <span>Appearance</span>
                    </button>
                    <button class="settings-tab" onclick="switchTab('seo')">
                        <i class="fas fa-search"></i>
                        <span>SEO</span>
                    </button>
                </div>

                <form action="<?php echo \App\Core\Config::url('settings'); ?>" method="POST"
                    enctype="multipart/form-data">

                    <!-- ═══════════════════════════════════════
                         TAB 1: Profile & Account
                         ═══════════════════════════════════════ -->
                    <div id="panel-profile" class="settings-panel active">

                        <!-- Avatar Card -->
                        <div class="settings-card">
                            <div class="settings-card-header">
                                <div class="settings-card-icon"><i class="fas fa-camera"></i></div>
                                <div>
                                    <h3>Profile Photo</h3>
                                    <p>Upload your avatar to personalize your page</p>
                                </div>
                            </div>
                            <div class="avatar-settings-row">
                                <div class="avatar-circle">
                                    <?php if ($user['avatar']): ?>
                                        <img src="<?php echo \App\Core\Config::upload('avatars/' . $user['avatar']); ?>"
                                            alt="Avatar">
                                    <?php else: ?>
                                        <i class="fas fa-user"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="avatar-info">
                                    <h4><?php echo htmlspecialchars($user['display_name'] ?: $user['username']); ?></h4>
                                    <p>Max 2MB — JPG, PNG, WebP</p>
                                    <label for="avatar" class="btn cursor-pointer">
                                        <i class="fas fa-upload"></i> Change Photo
                                    </label>
                                    <input type="file" id="avatar" name="avatar" class="hidden-input" accept="image/*">
                                </div>
                            </div>
                        </div>

                        <!-- Account Details Card -->
                        <div class="settings-card">
                            <div class="settings-card-header">
                                <div class="settings-card-icon"><i class="fas fa-id-card"></i></div>
                                <div>
                                    <h3>Account Details</h3>
                                    <p>Your basic account information</p>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="display_name">Display Name</label>
                                    <input type="text" id="display_name" name="display_name"
                                        value="<?php echo htmlspecialchars($user['display_name'] ?? ''); ?>"
                                        placeholder="Your display name">
                                </div>
                                <div class="form-group">
                                    <label for="username">Username</label>
                                    <input type="text" id="username" name="username"
                                        value="<?php echo htmlspecialchars($user['username']); ?>" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="email">Email Address</label>
                                <input type="email" id="email" name="email"
                                    value="<?php echo htmlspecialchars($user['email']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="bio">Bio <span class="text-muted-normal">(Max 80 characters)</span></label>
                                <textarea id="bio" name="bio" rows="3" maxlength="80"
                                    placeholder="Tell people about yourself..."><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                            </div>
                        </div>

                        <!-- Security Card -->
                        <div class="settings-card">
                            <div class="settings-card-header">
                                <div class="settings-card-icon"><i class="fas fa-lock"></i></div>
                                <div>
                                    <h3>Security</h3>
                                    <p>Update your password — leave blank to keep current</p>
                                </div>
                            </div>
                            <div class="form-group mb-0">
                                <label for="password">New Password</label>
                                <input type="password" id="password" name="password" placeholder="••••••••" autocomplete="current-password">
                            </div>
                        </div>
                    </div>

                    <!-- ═══════════════════════════════════════
                         TAB 2: Appearance Engine
                         ═══════════════════════════════════════ -->
                    <div id="panel-appearance" class="settings-panel">

                        <!-- Theme Selection Card -->
                        <div class="settings-card">
                            <div class="settings-card-header">
                                <div class="settings-card-icon"><i class="fas fa-swatchbook"></i></div>
                                <div>
                                    <h3>Theme & Style</h3>
                                    <p>Choose a preset theme or customize your own colors</p>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="theme">Preset Theme</label>
                                    <select id="theme" name="theme" class="styled-select">
                                        <option value="dark" <?php echo ($user['theme'] ?? '') === 'dark' ? 'selected' : ''; ?>>Matrix Dark (Default)</option>
                                        <option value="light" <?php echo ($user['theme'] ?? '') === 'light' ? 'selected' : ''; ?>>Minimal Light</option>
                                        <option value="ocean" <?php echo ($user['theme'] ?? '') === 'ocean' ? 'selected' : ''; ?>>Ocean Blue</option>
                                        <option value="neon" <?php echo ($user['theme'] ?? '') === 'neon' ? 'selected' : ''; ?>>Cyber Neon</option>
                                        <option value="custom" <?php echo ($user['theme'] ?? '') === 'custom' ? 'selected' : ''; ?>>Custom (Manual)</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="font_family">Font Family</label>
                                    <select id="font_family" name="font_family" class="styled-select">
                                        <option value="'Nunito', sans-serif" <?php echo ($user['font_family'] ?? '') === "'Nunito', sans-serif" ? 'selected' : ''; ?>>Nunito (Modern)</option>
                                        <option value="'Inter', sans-serif" <?php echo ($user['font_family'] ?? '') === "'Inter', sans-serif" ? 'selected' : ''; ?>>Inter (Clean)</option>
                                        <option value="'Roboto', sans-serif" <?php echo ($user['font_family'] ?? '') === "'Roboto', sans-serif" ? 'selected' : ''; ?>>Roboto (Tech)</option>
                                        <option value="'Outfit', sans-serif" <?php echo ($user['font_family'] ?? '') === "'Outfit', sans-serif" ? 'selected' : ''; ?>>Outfit (Premium)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="button_radius">Button Shape</label>
                                <select id="button_radius" name="button_radius" class="styled-select">
                                    <option value="0px" <?php echo ($user['button_radius'] ?? '') === '0px' ? 'selected' : ''; ?>>Square</option>
                                    <option value="12px" <?php echo ($user['button_radius'] ?? '') === '12px' ? 'selected' : ''; ?>>Rounded Corners</option>
                                    <option value="50px" <?php echo ($user['button_radius'] ?? '') === '50px' ? 'selected' : ''; ?>>Pill-shaped</option>
                                </select>
                            </div>
                        </div>

                        <!-- Background Card -->
                        <div class="settings-card">
                            <div class="settings-card-header">
                                <div class="settings-card-icon"><i class="fas fa-image"></i></div>
                                <div>
                                    <h3>Background</h3>
                                    <p>Set a solid color, image, or video loop as your page background</p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="bg_type">Background Type</label>
                                <select id="bg_type" name="bg_type" class="styled-select"
                                    onchange="const isMedia = this.value === 'media'; document.getElementById('bg_media_group').style.display = isMedia ? 'block' : 'none'; document.getElementById('bg_color_group').style.display = isMedia ? 'none' : 'block';">
                                    <option value="color" <?php echo ($user['bg_type'] ?? 'color') === 'color' ? 'selected' : ''; ?>>Solid Color / Preset Theme</option>
                                    <option value="media" <?php echo in_array($user['bg_type'] ?? '', ['image', 'video']) ? 'selected' : ''; ?>>Custom Media (Image/Video)</option>
                                </select>
                            </div>
                            <div id="bg_media_group" class="form-group"
                                style="<?php echo in_array($user['bg_type'] ?? 'color', ['image', 'video']) ? 'display: block;' : 'display: none;'; ?>">
                                <label>Upload Background <span class="text-muted-normal">(Max 10MB)</span></label>
                                <label for="bg_media" class="file-upload-zone" style="display: block;">
                                    <i class="fas fa-cloud-upload-alt" style="display: block;"></i>
                                    <p>Click to select a file — JPG, PNG, WebP or MP4</p>
                                    <div id="bg_preview_container"
                                        style="<?php echo !empty($user['bg_media']) ? 'display: block;' : 'display: none;'; ?> margin-top: 15px; border-radius: 8px; overflow: hidden; max-width: 200px; margin-left: auto; margin-right: auto; border: 1px solid rgba(255,255,255,0.1);">
                                        <?php if (!empty($user['bg_media'])): ?>
                                            <?php if (($user['bg_type'] ?? '') === 'video'): ?>
                                                <video id="bg_preview_video"
                                                    src="<?php echo \App\Core\Config::url('uploads/backgrounds/' . $user['bg_media']); ?>"
                                                    muted loop autoplay style="width: 100%; display: block;"></video>
                                                <img id="bg_preview_img" style="width: 100%; display: none;">
                                            <?php else: ?>
                                                <img id="bg_preview_img"
                                                    src="<?php echo \App\Core\Config::url('uploads/backgrounds/' . $user['bg_media']); ?>"
                                                    style="width: 100%; display: block;">
                                                <video id="bg_preview_video" muted loop autoplay
                                                    style="width: 100%; display: none;"></video>
                                            <?php endif; ?>
                                                 <p id="bg_preview_filename" style="display: none;"></p>
                                        <?php else: ?>
                                            <img id="bg_preview_img" style="width: 100%; display: none;">
                                            <video id="bg_preview_video" muted loop autoplay
                                                style="width: 100%; display: none;"></video>
                                             <p id="bg_preview_filename" style="display: none;"></p>
                                        <?php endif; ?>
                                    </div>
                                </label>
                                <input type="file" id="bg_media" name="bg_media" class="hidden-input"
                                    accept="image/jpeg, image/png, image/webp, video/mp4"
                                    onchange="handleBgPreview(this)">
                            </div>
                        </div>

                        <!-- Colors Card -->
                        <div class="settings-card">
                            <div class="settings-card-header">
                                <div class="settings-card-icon"><i class="fas fa-droplet"></i></div>
                                <div>
                                    <h3>Color Palette</h3>
                                    <p>Fine-tune your page colors</p>
                                </div>
                            </div>
                            <div class="color-grid">
                                <div class="color-picker-item" id="bg_color_group"
                                    style="<?php echo ($user['bg_type'] ?? 'color') !== 'color' ? 'display: none;' : ''; ?>">
                                    <label for="bg_color">Background</label>
                                    <input type="color" id="bg_color" name="bg_color"
                                        value="<?php echo $user['bg_color'] ?? '#000000'; ?>">
                                </div>
                                <div class="color-picker-item">
                                    <label for="button_bg_color">Button BG</label>
                                    <input type="color" id="button_bg_color" name="button_bg_color"
                                        value="<?php echo $user['button_bg_color'] ?? '#dc2726'; ?>">
                                </div>
                                <div class="color-picker-item">
                                    <label for="button_text_color">Button Text</label>
                                    <input type="color" id="button_text_color" name="button_text_color"
                                        value="<?php echo $user['button_text_color'] ?? '#ffffff'; ?>">
                                </div>
                                <div class="color-picker-item">
                                    <label for="font_color">Font Color</label>
                                    <input type="color" id="font_color" name="font_color"
                                        value="<?php echo $user['font_color'] ?? '#ffffff'; ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ═══════════════════════════════════════
                         TAB 3: SEO & Metadata
                         ═══════════════════════════════════════ -->
                    <div id="panel-seo" class="settings-panel">
                        <div class="settings-card">
                            <div class="settings-card-header">
                                <div class="settings-card-icon"><i class="fas fa-chart-line"></i></div>
                                <div>
                                    <h3>SEO & Sharing Metadata</h3>
                                    <p>Customize how your page appears on Google, WhatsApp, and social media</p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="seo_title">SEO Title</label>
                                <input type="text" id="seo_title" name="seo_title"
                                    value="<?php echo htmlspecialchars($user['seo_title'] ?? ''); ?>"
                                    placeholder="e.g. Ahmad AlHashmi | Professional Photographer">
                            </div>
                            <div class="form-group">
                                <label for="seo_description">Meta Description</label>
                                <textarea id="seo_description" name="seo_description" rows="3"
                                    placeholder="e.g. Check out my latest photography portfolio and social links."><?php echo htmlspecialchars($user['seo_description'] ?? ''); ?></textarea>
                            </div>

                            <!-- SEO Preview -->
                            <div
                                style="margin-top: 20px; padding: 18px; background: rgba(255,255,255,0.03); border-radius: 12px; border: 1px solid rgba(255,255,255,0.06);">
                                <p
                                    style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.1em; color: var(--text-muted); margin-bottom: 10px;">
                                    <i class="fab fa-google" style="margin-right: 5px;"></i> Google Preview
                                </p>
                                <p style="color: #8ab4f8; font-size: 1rem; margin-bottom: 2px;" id="seoPreviewTitle">
                                    <?php echo htmlspecialchars($user['seo_title'] ?: ($user['display_name'] ?: $user['username'])); ?>
                                </p>
                                <p style="color: #bdc1c6; font-size: 0.82rem; line-height: 1.4;" id="seoPreviewDesc">
                                    <?php echo htmlspecialchars($user['seo_description'] ?: 'Check out my links and social profiles.'); ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- ═══ Save Button (Sticky) ═══ -->
                    <div class="save-section">
                        <button type="submit" class="save-btn-full">
                            <i class="fas fa-save"></i> Save All Changes
                        </button>
                    </div>

                        </form>
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
            </div> <!-- End dashboard-layout -->

        <!-- Cropping Modal -->
        <div id="cropModal" class="modal">
            <div class="modal-content">
                <h3 style="margin-bottom:15px;">Crop Your Avatar</h3>
                <div class="crop-container">
                    <img id="imageToCrop" style="max-width:100%;">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" id="cancelCrop"
                        style="background:var(--gray); width:auto; padding:8px 20px;">Cancel</button>
                    <button type="button" class="btn" id="confirmCrop" style="width:auto; padding:8px 20px;">Crop &
                        Save</button>
                </div>
            </div>
        </div>
        <script src="<?php echo \App\Core\Config::asset('js/cropper.min.js'); ?>"></script>
        <script>
            // Pass dynamic PHP data to the external JavaScript file
            window.SettingsConfig = {
                defaultSeoTitle: '<?php echo htmlspecialchars($user['display_name'] ?: $user['username']); ?>'
            };
        </script>
        <script src="<?php echo \App\Core\Config::asset('js/toast.js'); ?>"></script>
        <script src="<?php echo \App\Core\Config::asset('js/settings.js'); ?>"></script>

        <script src="<?php echo \App\Core\Config::asset('js/main.js'); ?>"></script>
</body>

</html>