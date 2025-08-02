/**
 * Main App JavaScript - CSP Compliant
 * Handles page loading overlay and navigation
 */

// Helper function to hide loading overlay
function hidePageLoadingOverlay() {
    const overlay = document.getElementById('pageLoadingOverlay');
    if (overlay) {
        overlay.classList.add('hidden');
        overlay.classList.remove('flex');
    }
}

// Helper function to show loading overlay
function showPageLoadingOverlay() {
    const overlay = document.getElementById('pageLoadingOverlay');
    if (overlay) {
        overlay.classList.remove('hidden');
        overlay.classList.add('flex', 'items-center', 'justify-center');
    }
}

// Hide overlay when page loads (initial load and refresh)
window.addEventListener('load', hidePageLoadingOverlay);

// Hide overlay when navigating with browser back/forward buttons
window.addEventListener('popstate', hidePageLoadingOverlay);

// Force hide overlay when page is about to unload but navigation is canceled
window.addEventListener('beforeunload', function() {
    setTimeout(hidePageLoadingOverlay, 500);
});

// Force hide overlay in case it gets stuck during navigation
// This timer will check every second if the overlay is still visible
let overlayCheckInterval;
function startOverlayCheckInterval() {
    clearInterval(overlayCheckInterval);
    let checkCount = 0;
    overlayCheckInterval = setInterval(() => {
        const overlay = document.getElementById('pageLoadingOverlay');
        if (overlay && !overlay.classList.contains('hidden') && checkCount > 3) {
            console.log('Force hiding overlay after timeout');
            hidePageLoadingOverlay();
            clearInterval(overlayCheckInterval);
        }
        checkCount++;
        if (checkCount > 10) {
            clearInterval(overlayCheckInterval);
        }
    }, 1000);
}

document.addEventListener('DOMContentLoaded', function() {
    // Hide overlay on initial page load
    hidePageLoadingOverlay();
    console.log('Page fully loaded - overlay hidden');
    
    // Start interval check for stuck overlay
    startOverlayCheckInterval();

    // Block clicks on nav-links and delay page change
    document.querySelectorAll('nav a, .sidebar a').forEach(link => {
        link.addEventListener('click', function(e) {
            // Only handle internal links (no target _blank, not anchor, no modifier key)
            if (
                this.target === '_blank' ||
                (this.href && (this.href.startsWith('javascript:') || this.href === '#' || this.href.includes('#'))) ||
                e.ctrlKey || e.shiftKey || e.metaKey || e.altKey
            ) return;
            
            e.preventDefault();
            showPageLoadingOverlay();
            console.log('Navigation link clicked - showing overlay');
            
            // Start interval check for stuck overlay
            startOverlayCheckInterval();
            
            // Small delay to ensure the overlay is visible
            setTimeout(() => {
                window.location.href = this.href;
            }, 150);
        });
    });
    
    // Handle form submissions
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function() {
            // Don't show overlay for forms with data-no-loading attribute
            if (this.hasAttribute('data-no-loading')) return;
            
            showPageLoadingOverlay();
            console.log('Form submitted - showing overlay');
        });
    });
    
    // When clicking Delete Account button in modal, turn off loading overlay immediately
    document.querySelectorAll('button').forEach(btn => {
        if (btn.textContent.trim() === 'Delete Account') {
            btn.addEventListener('click', function() {
                hidePageLoadingOverlay();
            });
        }
    });
});
