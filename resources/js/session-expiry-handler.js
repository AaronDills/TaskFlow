/**
 * Session Expiry Handler
 *
 * Detects when a user's session has expired during API calls and shows
 * a toast notification before redirecting to the login page.
 */

(function() {
    'use strict';

    // Configuration
    const TOAST_DURATION = 3000; // How long to show the toast before redirecting (ms)
    const LOGIN_URL = '/login';

    // Track if we're already handling a session expiry to prevent multiple toasts
    let isHandlingExpiry = false;

    /**
     * Create and show the session expiry toast notification
     */
    function showSessionExpiryToast() {
        // Prevent multiple toasts
        if (isHandlingExpiry) {
            return;
        }
        isHandlingExpiry = true;

        // Create toast container if it doesn't exist
        let toastContainer = document.getElementById('session-expiry-toast');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'session-expiry-toast';
            toastContainer.className = 'fixed top-4 right-4 z-[9999] transform transition-all duration-300 ease-out translate-x-full opacity-0';
            toastContainer.innerHTML = `
                <div class="bg-red-600 text-white px-6 py-4 rounded-lg shadow-lg flex items-center gap-3 max-w-sm">
                    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <div>
                        <p class="font-semibold">Session Expired</p>
                        <p class="text-sm text-red-100">You've been signed out. Redirecting to login...</p>
                    </div>
                </div>
            `;
            document.body.appendChild(toastContainer);
        }

        // Trigger animation after a small delay to ensure the element is in the DOM
        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                toastContainer.classList.remove('translate-x-full', 'opacity-0');
                toastContainer.classList.add('translate-x-0', 'opacity-100');
            });
        });

        // Redirect to login after the toast duration
        setTimeout(() => {
            window.location.href = LOGIN_URL;
        }, TOAST_DURATION);
    }

    /**
     * Check if a response indicates session expiry
     * @param {Response} response - The fetch response object
     * @returns {boolean}
     */
    function isSessionExpired(response) {
        // 401 Unauthorized - session invalid/expired
        // 419 Page Expired - CSRF token mismatch (Laravel-specific, often indicates session expiry)
        return response.status === 401 || response.status === 419;
    }

    /**
     * Wrap the native fetch to intercept session expiry responses
     */
    function wrapFetch() {
        const originalFetch = window.fetch;

        window.fetch = async function(...args) {
            try {
                const response = await originalFetch.apply(this, args);

                // Check for session expiry on non-OK responses
                if (isSessionExpired(response)) {
                    showSessionExpiryToast();
                    // Return a rejected promise to stop further processing
                    return Promise.reject(new Error('Session expired'));
                }

                return response;
            } catch (error) {
                // Re-throw other errors
                throw error;
            }
        };
    }

    /**
     * Initialize the session expiry handler
     */
    function init() {
        // Only run in browser environment
        if (typeof window === 'undefined') {
            return;
        }

        // Wrap fetch to intercept responses
        wrapFetch();

        // Also add an event listener for handling session expiry from other sources
        // (e.g., if a component needs to manually trigger the toast)
        window.addEventListener('session-expired', function() {
            showSessionExpiryToast();
        });
    }

    // Auto-initialize when the DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Export for manual usage if needed
    window.SessionExpiryHandler = {
        showToast: showSessionExpiryToast,
        isSessionExpired: isSessionExpired
    };
})();
