importScripts('https://www.gstatic.com/firebasejs/9.6.10/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/9.6.10/firebase-messaging-compat.js');

firebase.initializeApp({
  apiKey: "AIzaSyA55zxwCWlVp213HgToEhScZXrh9F8ch2g",
  authDomain: "comunication-chatbot.firebaseapp.com",
  projectId: "comunication-chatbot",
  storageBucket: "comunication-chatbot.firebasestorage.app",
  messagingSenderId: "274571479905",
  appId: "1:274571479905:web:092833bb545435e1079320",
  measurementId: "G-TFWCSET1EK"
});

const messaging = firebase.messaging();

messaging.onBackgroundMessage(function(payload) {
  console.log('[firebase-messaging-sw.js] Received background message ', payload);
  const notificationTitle = payload.notification.title;
  const notificationOptions = {
    body: payload.notification.body,
    icon: '/icon.png' // Optional: replace with your own icon
  };

  self.registration.showNotification(notificationTitle, notificationOptions);
});
