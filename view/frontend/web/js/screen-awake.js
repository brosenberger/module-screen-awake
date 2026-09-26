/**
 * Keeps the screen awake while the customer wants it, via the Screen Wake Lock API.
 * The browser drops the lock whenever the page is hidden; it is requested again
 * when the page becomes visible. Unsupported browsers never see the button.
 */
define([], function () {
    'use strict';

    function initScreenAwake(button) {
        if (!('wakeLock' in navigator)) {
            return; // unsupported: button stays hidden
        }

        let wanted = false;  // what the customer chose
        let sentinel = null; // what the browser currently grants

        async function acquire() {
            try {
                sentinel = await navigator.wakeLock.request('screen');
                sentinel.addEventListener('release', () => { sentinel = null; });
            } catch (err) {
                setWanted(false); // hidden tab, power saving or Permissions-Policy
            }
        }

        function setWanted(on) {
            wanted = on;
            button.setAttribute('aria-pressed', String(on));
            document.body.classList.toggle('is-screen-awake', on);
            document.dispatchEvent(new CustomEvent('screenawake:change', { detail: { on } }));
        }

        button.hidden = false;
        button.addEventListener('click', () => {
            setWanted(!wanted);
            if (wanted) {
                acquire();
            } else if (sentinel) {
                sentinel.release();
            }
        });

        document.addEventListener('visibilitychange', () => {
            if (wanted && !sentinel && document.visibilityState === 'visible') {
                acquire();
            }
        });
    }

    return function (config, element) {
        initScreenAwake(element);
    };
});
