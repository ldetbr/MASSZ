/**
 * MASSZ v2.0 - Utility JS Helpers
 */

var MasszUtils = {
    /**
     * Show a custom notification banner at the top of the Zabbix UI
     * @param {string} message 
     * @param {string} type 'success' | 'error' | 'warning'
     */
    showNotification: function(message, type) {
        // Remove existing massz notifications first
        var existing = document.querySelectorAll('.massz-notification');
        existing.forEach(function(el) { el.remove(); });

        var banner = document.createElement('div');
        banner.className = 'massz-notification msg-details msg-' + (type === 'success' ? 'good' : 'bad') + ' massz-notif-toast';
        banner.style.position = 'fixed';
        banner.style.top = '50px';
        banner.style.right = '20px';
        banner.style.zIndex = '99999';
        banner.style.minWidth = '300px';
        banner.style.padding = '12px 20px';
        banner.style.borderRadius = '4px';
        banner.style.boxShadow = '0 4px 15px rgba(0,0,0,0.15)';
        banner.style.animation = 'massz-fade-in 0.3s ease';

        var title = document.createElement('span');
        title.className = 'msg-details-border';
        title.style.fontWeight = 'bold';
        title.innerText = type === 'success' ? 'Success: ' : 'Error: ';
        banner.appendChild(title);

        var text = document.createTextNode(message);
        banner.appendChild(text);

        // Add CSS keyframes for fade in
        if (!document.getElementById('massz-toast-animation')) {
            var style = document.createElement('style');
            style.id = 'massz-toast-animation';
            style.innerHTML = '@keyframes massz-fade-in { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }';
            document.head.appendChild(style);
        }

        document.body.appendChild(banner);

        // Auto remove after 5 seconds
        setTimeout(function() {
            banner.style.transition = 'opacity 0.5s ease';
            banner.style.opacity = '0';
            setTimeout(function() {
                banner.remove();
            }, 500);
        }, 5000);
    }
};
