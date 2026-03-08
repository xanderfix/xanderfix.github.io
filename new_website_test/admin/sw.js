self.addEventListener('install', () => {
    self.skipWaiting();
});

self.addEventListener('push', (event) => {
    const json = event.data.json();
    const showing = self.registration.showNotification(json.title, {
        body: json.body,
        data: json.data
    });

    event.waitUntil(showing);
});

self.addEventListener('notificationclick', (event) => {
    const clickedNotification = event.notification;
    if (!clickedNotification) return;
    clickedNotification.close();

    if (clickedNotification.data && clickedNotification.data.url) {
        const urlToOpen = new URL(clickedNotification.data.url, self.location.href).href;

        const opening = clients.matchAll({
            type: 'window',
            includeUncontrolled: true,
        }).then((windowClients) => {
            let matchingClient = null;

            for (let i = 0; i < windowClients.length; i++) {
                const windowClient = windowClients[i];
                if (windowClient.url === urlToOpen) {
                    matchingClient = windowClient;
                    break;
                }
            }

            if (matchingClient) {
                matchingClient.postMessage('refresh');
                return matchingClient.focus();
            } else {
                return clients.openWindow(urlToOpen);
            }
        });

        event.waitUntil(opening);
    }
});

