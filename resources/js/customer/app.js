import React from 'react'
import ReactDOM from 'react-dom'
import { BrowserRouter } from 'react-router-dom'

import { FirebaseAppProvider } from './lib/firebase'
import Echo from 'laravel-echo'
import Pusher from 'pusher-js'
import { QueryClient, QueryClientProvider } from 'react-query'

import App from './pages/App'

window.React = React

window.Pusher = Pusher
window.Echo = new Echo({
    broadcaster: "pusher",
    key: process.env.MIX_PUSHER_APP_KEY,    
    cluster: process.env.MIX_PUSHER_APP_CLUSTER,
    forceTLS: true
})

ReactDOM.render(
    <React.StrictMode>
        <BrowserRouter>
            <QueryClientProvider client={new QueryClient()}>
                <FirebaseAppProvider config={{
                    apiKey: process.env.MIX_FIREBASE_API_KEY,
                    authDomain: process.env.MIX_FIREBASE_AUTH_DOMAIN,
                    projectId: process.env.MIX_FIREBASE_PROJECT_ID,
                    storageBucket: process.env.MIX_FIREBASE_STORAGE_BUCKET,
                    messagingSenderId: process.env.MIX_FIREBASE_MESSAGING_SENDER_ID,
                    appId: process.env.MIX_FIREBASE_APP_ID,
                    measurementId: process.env.MIX_FIREBASE_MEASUREMENT_ID
                }}>
                    <App />
                </FirebaseAppProvider>
            </QueryClientProvider>
        </BrowserRouter>
    </React.StrictMode>,
    document.getElementById('root')
)