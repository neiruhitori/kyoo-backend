import {
    createContext,
    useMemo,
    useEffect,
    useContext,
    useState
} from 'react'

import { getApp, initializeApp } from 'firebase/app'
import { getToken, getMessaging } from 'firebase/messaging'

const FirebaseAppContext = createContext()

export function FirebaseAppProvider(props) {
    const firebaseApp = useMemo(() => {
        try {
            return getApp()
        } catch (err) {
            if (!props.config) throw new Error('No firebase config provided')

            return initializeApp(props.config)
        }
    }, [props.config])

    return <FirebaseAppContext.Provider value={firebaseApp} {...props} />
}

export function useFirebaseApp() {
    const app = useContext(FirebaseAppContext)

    if (!app) {
        throw new Error('Can\'t call useFirebaseApp outside FirebaseAppProvider component')
    }

    return app
}

export function useMessaging() {
    const app = useContext(FirebaseAppContext)

    if (!app) {
        throw new Error('Can\'t call useFirebaseApp outside FirebaseAppProvider component')
    }

    return getMessaging(app)
}

export function useToken(messaging, vapidKey) {
    const [token, setToken] = useState('')

    useEffect(() => {
        getToken(messaging, { vapidKey })
            .then(currentToken => setToken(currentToken))
    }, [messaging])

    return useMemo(() => token, [token])
}