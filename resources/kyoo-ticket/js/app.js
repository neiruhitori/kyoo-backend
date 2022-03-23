import React from 'react'
import ReactDOM from 'react-dom'

import App from './components/App'

window.React = React

ReactDOM.render(
    <React.StrictMode>
        <App />
    </React.StrictMode>,
    document.getElementById('root')
)