// ═══ Tab Switching Logic ═══
function switchTab(tabName, eventTriggered = true) {
    // Deactivate all tabs and panels
    document.querySelectorAll('.settings-tab').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.settings-panel').forEach(p => p.classList.remove('active'));

    // Activate selected
    document.getElementById('panel-' + tabName).classList.add('active');

    // If triggered by click, update the button and save to localStorage
    if (eventTriggered && event) {
        event.currentTarget.classList.add('active');
        localStorage.setItem('activeSettingsTab', tabName);
    } else {
        // Find the button manually if called programmatically
        const btn = document.querySelector(`button[onclick*="'${tabName}'"]`);
        if (btn) btn.classList.add('active');
    }
}

// Restore last active tab on load
window.addEventListener('DOMContentLoaded', () => {
    const lastTab = localStorage.getItem('activeSettingsTab') || 'profile';
    switchTab(lastTab, false);
});

// ═══ SEO Live Preview ═══
const seoTitle = document.getElementById('seo_title');
const seoDesc = document.getElementById('seo_description');
const previewTitle = document.getElementById('seoPreviewTitle');
const previewDesc = document.getElementById('seoPreviewDesc');

if (seoTitle && previewTitle) {
    seoTitle.addEventListener('input', () => {
        previewTitle.textContent = seoTitle.value || window.SettingsConfig.defaultSeoTitle;
    });
}
if (seoDesc && previewDesc) {
    seoDesc.addEventListener('input', () => {
        previewDesc.textContent = seoDesc.value || 'Check out my links and social profiles.';
    });
}

// ═══ Cropper.js Logic ═══
let cropper;
const cropModal = document.getElementById('cropModal');
const imageToCrop = document.getElementById('imageToCrop');
const avatarInput = document.querySelector('input[type="file"]#avatar');
const confirmCrop = document.getElementById('confirmCrop');
const cancelCrop = document.getElementById('cancelCrop');

if (avatarInput) {
    // When a file is selected
    avatarInput.addEventListener('change', function (e) {
        const files = e.target.files;
        if (files && files.length > 0) {
            const reader = new FileReader();
            reader.onload = function (event) {
                imageToCrop.src = event.target.result;
                cropModal.style.display = 'flex';

                if (cropper) cropper.destroy();
                cropper = new Cropper(imageToCrop, {
                    aspectRatio: 1,
                    viewMode: 1,
                    background: false
                });
            };
            reader.readAsDataURL(files[0]);
        }
    });
}

if (cancelCrop) {
    // Cancel button
    cancelCrop.onclick = () => {
        cropModal.style.display = 'none';
        avatarInput.value = '';
        if (cropper) {
            cropper.destroy();
            cropper = null;
        }
    };
}

if (confirmCrop) {
    // Confirm Crop button
    confirmCrop.onclick = () => {
        if (!cropper) return;

        const canvas = cropper.getCroppedCanvas({
            width: 400,
            height: 400
        });

        canvas.toBlob((blob) => {
            // Update preview
            const avatarImg = document.querySelector('.avatar-circle img');
            if (avatarImg) {
                avatarImg.src = canvas.toDataURL();
            } else {
                const circle = document.querySelector('.avatar-circle');
                circle.innerHTML = '<img src="' + canvas.toDataURL() + '" alt="Avatar">';
            }

            // Create a new file object from the blob to replace the input file
            const croppedFile = new File([blob], 'cropped_avatar.png', { type: 'image/png' });

            // Use DataTransfer to inject the new file into the input
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(croppedFile);
            avatarInput.files = dataTransfer.files;

            cropModal.style.display = 'none';
            if (cropper) {
                cropper.destroy();
                cropper = null;
            }
        });
    };
}

// ═══ Bio Line Limit ═══
const bioTextarea = document.getElementById('bio');
if (bioTextarea) {
    const maxLines = 3;

    bioTextarea.addEventListener('input', function () {
        const lines = this.value.split('\n');
        if (lines.length > maxLines) {
            this.value = lines.slice(0, maxLines).join('\n');
        }
    });

    bioTextarea.addEventListener('paste', function (e) {
        setTimeout(() => {
            const lines = this.value.split('\n');
            if (lines.length > maxLines) {
                this.value = lines.slice(0, maxLines).join('\n');
            }
        }, 0);
    });
}

// Background Live Preview Handler
function handleBgPreview(input) {
    const container = document.getElementById('bg_preview_container');
    const imgPreview = document.getElementById('bg_preview_img');
    const videoPreview = document.getElementById('bg_preview_video');
    const filenameText = document.getElementById('bg_preview_filename');

    if (input.files && input.files[0]) {
        const file = input.files[0];
        const url = URL.createObjectURL(file);

        container.style.display = 'block';
        filenameText.textContent = 'Selected: ' + file.name;

        if (file.type.startsWith('video/')) {
            imgPreview.style.display = 'none';
            videoPreview.src = url;
            videoPreview.style.display = 'block';
            videoPreview.play();
        } else {
            videoPreview.style.display = 'none';
            videoPreview.pause();
            imgPreview.src = url;
            imgPreview.style.display = 'block';
        }
    }
}

// --- Theme Preset Loader ---
const themeSelect = document.getElementById('theme');
if (themeSelect) {
    const colorInputs = {
        bg_color: document.getElementById('bg_color'),
        button_bg_color: document.getElementById('button_bg_color'),
        button_text_color: document.getElementById('button_text_color'),
        font_color: document.getElementById('font_color')
    };

    const themePresets = {
        dark: { bg: '#000000', btn: '#dc2726', btnText: '#ffffff', font: '#ffffff' },
        light: { bg: '#ffffff', btn: '#ffffff', btnText: '#000000', font: '#000000' },
        ocean: { bg: '#0f172a', btn: '#38bdf8', btnText: '#ffffff', font: '#f8fafc' },
        neon: { bg: '#0a0a0a', btn: '#00f2ff', btnText: '#ffffff', font: '#ffffff' }
    };

    themeSelect.addEventListener('change', function () {
        const preset = themePresets[this.value];
        if (preset) {
            colorInputs.bg_color.value = preset.bg;
            colorInputs.button_bg_color.value = preset.btn;
            colorInputs.button_text_color.value = preset.btnText;
            colorInputs.font_color.value = preset.font;

            // Trigger custom colors group visibility
            document.getElementById('bg_color_group').style.display = 'block';
        }
    });

    // If user touches any color picker, automatically switch dropdown to "Custom"
    Object.values(colorInputs).forEach(input => {
        input.addEventListener('input', () => {
            themeSelect.value = 'custom';
        });
    });
}
