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
    apiKey: "AIzaSyAixUDbtnQmD3VxNS1HzrLmJK8JCAS9TEc",
    authDomain: "kyoo-app-d17a0.firebaseapp.com",
    projectId: "kyoo-app-d17a0",
    storageBucket: "kyoo-app-d17a0.appspot.com",
    messagingSenderId: "992312467203",
    appId: "1:992312467203:web:0a0c399ced6515051d1336",
    measurementId: "G-2XMMRW9N0T"
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