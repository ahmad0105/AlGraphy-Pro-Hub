/**
 * Toast Notification Engine
 * AlGraphy Pro Hub
 */

const Toast = {
    init() {
        // Create container if not exists
        if (!document.getElementById('toast-container')) {
            const container = document.createElement('div');
            container.id = 'toast-container';
            document.body.appendChild(container);
        }
    },

    show(message, type = 'success', duration = 4000) {
        this.init();
        const container = document.getElementById('toast-container');

        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        
        const icon = type === 'success' ? 'fa-check-circle' : 
                     type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle';

        toast.innerHTML = `
            <i class="fas ${icon}"></i>
            <span class="toast-message">${message}</span>
        `;

        container.appendChild(toast);

        // Animate in
        setTimeout(() => toast.classList.add('show'), 10);

        // Auto remove
        const timer = setTimeout(() => this.hide(toast), duration);

        // Manual close on click
        toast.onclick = () => {
            clearTimeout(timer);
            this.hide(toast);
        };
    },

    hide(toast) {
        toast.classList.add('fade-out');
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 300);
    }
};

// Expose globally
window.showToast = (msg, type, dur) => Toast.show(msg, type, dur);
