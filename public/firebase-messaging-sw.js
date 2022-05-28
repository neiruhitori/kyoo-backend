self.addEventListener('notificationclick', e => {
    // Close the notification popout
    e.notification.close();

    // Get all the Window clients
    e.waitUntil(clients.matchAll({ type: 'window' }).then(clientsArr => {
        // If a Window tab matching the targeted URL already exists, focus that;
        const hadWindowToFocus = clientsArr.some(windowClient => windowClient.url === e.notification.data.url ? (windowClient.focus(), true) : false);
        // Otherwise, open a new tab to the applicable URL and focus it.
        if (!hadWindowToFocus) clients.openWindow(e.notification.data.url).then(windowClient => windowClient ? windowClient.focus() : null);
    }));
});

importScripts('https://www.gstatic.com/firebasejs/8.10.0/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.10.0/firebase-messaging.js');

// Initialize the Firebase app in the service worker by passing in
// your app's Firebase config object.
// https://firebase.google.com/docs/web/setup#config-object
firebase.initializeApp({
    apiKey: process.env.MIX_FIREBASE_API_KEY,
    authDomain: process.env.MIX_FIREBASE_AUTH_DOMAIN,
    projectId: process.env.MIX_FIREBASE_PROJECT_ID,
    storageBucket: process.env.MIX_FIREBASE_STORAGE_BUCKET,
    messagingSenderId: process.env.MIX_FIREBASE_MESSAGING_SENDER_ID,
    appId: process.env.MIX_FIREBASE_APP_ID,
    measurementId: process.env.MIX_FIREBASE_MEASUREMENT_ID
});

// Retrieve an instance of Firebase Messaging so that it can handle background
// messages.
const messaging = firebase.messaging();

messaging.onBackgroundMessage(({ data }) => {
    console.log(JSON.parse(data.data))

    const notificationTitle = data.title;
    const notificationOptions = {
        body: data.body,
        icon: '/img/favico.png',
        data: JSON.parse(data.data)
    };
  
    self.registration.showNotification(
        notificationTitle,
        notificationOptions,
    );
});