import React from 'react'
import ReactDOM from 'react-dom'
import { BrowserRouter } from 'react-router-dom'

import { FirebaseAppProvider } from './lib/firebase'

import App from './pages/App'

window.React = React

ReactDOM.render(
    <React.StrictMode>
        <BrowserRouter>
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
        </BrowserRouter>
    </React.StrictMode>,
    document.getElementById('root')
)