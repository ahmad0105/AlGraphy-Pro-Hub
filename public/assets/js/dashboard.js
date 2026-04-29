let isDraggingSocial = false;

function refreshPreview() {
    const preview = document.getElementById('profilePreview');
    if (preview) {
        // Appending a timestamp to bypass iframe caching
        const url = new URL(preview.src);
        url.searchParams.set('t', Date.now());
        preview.src = url.toString();
    }
}

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

if (socialForm) {
    socialForm.onsubmit = function (e) {
        e.preventDefault();
        const key = document.getElementById('socialKey').value;
        const value = document.getElementById('socialValue').value;

        const formData = new FormData();
        formData.append('key', key);
        formData.append('value', value);

        fetch(window.AppConfig.urls.updateSocial, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Social profile updated!', 'success');
                refreshPreview();
                setTimeout(() => location.reload(), 1500);
            } else {
                showToast('Error: ' + data.message, 'error');
                document.body.style.overflow = 'auto';
            }
        });
    };
}

// Modal Logic
const modal = document.getElementById('addLinkModal');
const btn = document.getElementById('addLinkBtn');
const close = document.getElementById('closeModal');
const iconOptions = document.querySelectorAll('.icon-option');
const selectedIconInput = document.getElementById('selectedIcon');

if (btn) {
    btn.onclick = () => {
        document.getElementById('modalTitle').innerText = 'Add New Link';
        document.getElementById('linkForm').action = window.AppConfig.urls.addLink;
        document.getElementById('linkId').value = '';
        document.getElementById('title').value = '';
        document.getElementById('subtitle').value = '';
        document.getElementById('url').value = '';
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    };
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
        document.getElementById('linkForm').action = window.AppConfig.urls.updateLink;
        document.getElementById('linkId').value = id;
        document.getElementById('title').value = title;
        document.getElementById('subtitle').value = subtitle;
        document.getElementById('url').value = url;

        iconOptions.forEach(opt => opt.classList.remove('selected'));
        const matchedIcon = document.querySelector(`.icon-option[data-icon="${icon}"]`) || document.querySelector('.icon-option[data-icon="fas fa-link"]');
        if (matchedIcon) {
            matchedIcon.classList.add('selected');
            selectedIconInput.value = matchedIcon.getAttribute('data-icon');
            matchedIcon.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    };
});

if (close) {
    close.onclick = () => {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    };
}

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

iconOptions.forEach(option => {
    option.addEventListener('click', function () {
        iconOptions.forEach(opt => opt.classList.remove('selected'));
        this.classList.add('selected');
        selectedIconInput.value = this.getAttribute('data-icon');
    });
});

const urlInput = document.getElementById('url');
if (urlInput) {
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

        // Auto-detect phone number
        if (/^\+?[0-9\s\-]{7,20}$/.test(url)) {
            const icon = 'fas fa-phone';
            selectedIconInput.value = icon;
            const option = document.querySelector(`.icon-option[data-icon="${icon}"]`);
            iconOptions.forEach(opt => opt.classList.remove('selected'));
            if (option) {
                option.classList.add('selected');
                option.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
            return;
        }

        // Auto-detect email
        if (/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(url)) {
            const icon = 'fas fa-envelope';
            selectedIconInput.value = icon;
            const option = document.querySelector(`.icon-option[data-icon="${icon}"]`);
            iconOptions.forEach(opt => opt.classList.remove('selected'));
            if (option) {
                option.classList.add('selected');
                option.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
            return;
        }

        for (const [domain, icon] of Object.entries(iconMappings)) {
            if (url.includes(domain)) {
                selectedIconInput.value = icon;
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
}

const toggles = document.querySelectorAll('.toggle-link');
toggles.forEach(toggle => {
    toggle.addEventListener('change', function () {
        const id = this.getAttribute('data-id');
        const formData = new FormData();
        formData.append('id', id);

        fetch(window.AppConfig.urls.toggleLink, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                showToast('Failed to update status.', 'error');
                this.checked = !this.checked;
            } else {
                showToast('Status updated!', 'success');
                refreshPreview();
            }
        });
    });
});

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

if (document.getElementById('socialGridDashboard')) {
    new SortableManager({
        containerId: 'socialGridDashboard',
        itemSelector: '.social-icon-item',
        orientation: 'horizontal',
        onDragStateChange: (state) => { isDraggingSocial = state; },
        onOrderChange: (order) => {
            const formData = new FormData();
            formData.append('order', JSON.stringify(order));
            fetch(window.AppConfig.urls.reorderSocials, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('Order saved!', 'success');
                    refreshPreview();
                } else {
                    showToast('Failed to save social order', 'error');
                }
            });
        }
    });
}

if (document.getElementById('linksList')) {
    new SortableManager({
        containerId: 'linksList',
        itemSelector: '.link-item',
        orientation: 'vertical',
        onOrderChange: (order) => {
            fetch(window.AppConfig.urls.reorderLinks, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ order: order })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('Links reordered!', 'success');
                    refreshPreview();
                } else {
                    showToast('Failed to save link order', 'error');
                }
            });
        }
    });
}
