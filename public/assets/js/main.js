document.addEventListener('DOMContentLoaded', () => {
    
    // 1. Auto-hide Alerts (Success/Error messages)
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        // Wait for 4 seconds then trigger the CSS animation
        setTimeout(() => {
            alert.classList.add('alert-hide');
            // Remove from DOM after CSS transition finishes (600ms)
            setTimeout(() => alert.remove(), 600);
        }, 4000);
    });

});
