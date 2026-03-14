!function () {
    const loading = document.getElementById('loading-notifications-settings');
    const installation = document.getElementById('installation-needed');
    const denied = document.getElementById('denied-notifications');
    const activate = document.getElementById('activate-notifications');
    const activateButton = document.getElementById('activate-notifications-button');
    const settings = document.getElementById('set-notifications');
    const settingsForm = document.getElementById('set-notifications-form');
    const error = document.getElementById('error-notifications');
    const subscriptionError = document.getElementById('subscription-error-notifications');
    const ecommerceCheck = document.getElementById('ecommerce_check');
    const lowStockCheck = document.getElementById('low_stock_check');
    const blogCheck = document.getElementById('blog_comments_check');
    const productsCheck = document.getElementById('products_comments_check');
    const pagesCheck = document.getElementById('pages_comments_check');
    const usersCheck = document.getElementById('users_check');

    function showSection(section) {
        loading.style.display = 'none';
        installation.style.display = 'none';
        denied.style.display = 'none';
        activate.style.display = 'none';
        settings.style.display = 'none';
        error.style.display = 'none';
        section.style.display = '';
    }

    function updatePushState(sw) {
        if ('pushManager' in sw) {
            sw.pushManager.permissionState(pushOptions).then((state) => {
                switch (state) {
                    case 'prompt': showActivate(sw); break;
                    case 'granted': askPermission(sw); break;
                    case 'denied': showSection(denied); break;
                    default: showSection(installation); break;
                }
            }).catch((err) => {
                showSection(installation);
            });
        }
        else {
            showSection(installation);    
        }
    }

    function showActivate(sw) {
        showSection(activate);
        if (typeof activateButton['onclick'] === 'function') {
            activateButton.removeEventListener('click');
        }
        activateButton.addEventListener('click', () => {
            askPermission(sw);
        });
    }

    function submitEvent(e) {
        e.preventDefault();
        saveSettings();
        return false;
    }

    function savePermission(pushSubscription) {
        fetch('notifications.php?permission', {
            method: 'POST',
            body: JSON.stringify(pushSubscription)
        }).then((response) => {
            if (!response.ok) throw 'not saved';
            showSection(settings);
            if (typeof settingsForm['onsubmit'] === 'function') {
                settingsForm.removeEventListener('submit');
            }
            settingsForm.addEventListener('submit', submitEvent);
        }).catch((err) => {
            showSection(error);
        });
    }

    function saveSettings() {
        showSection(loading);
        fetch('notifications.php?settings', {
            method: 'POST',
            body: JSON.stringify({
                ecommerce: ecommerceCheck.checked,
                low_stock: lowStockCheck.checked,
                blog_comments: blogCheck.checked,
                products_comments: productsCheck.checked,
                pages_comments: pagesCheck.checked,
                users: usersCheck.checked
            })
        }).then((response) => {
            if (!response.ok) throw 'not saved';
            showSection(settings);
        }).catch((err) => {
            showSection(error);
        });
    }

    function askPermission(sw) {
        showSection(loading);
        sw.pushManager.getSubscription().then((existingPushSubscription) => {
            sw.pushManager.subscribe(pushOptions).then((pushSubscription) => {
                if (pushSubscription) {
                    savePermission(pushSubscription);
                }
                else {
                    updatePushState(sw);
                }
            }).catch((err) => {
                if (existingPushSubscription === null) {
                    updatePushState(sw);
                }
                else {
                    showSection(subscriptionError);
                }
            }); 
        }).catch((err) => {
            showSection(error);
        });
    }

    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.getRegistration().then((sw) => {
            updatePushState(sw);
        }).catch((err) => {
            showSection(installation);
        });
    }
}();
