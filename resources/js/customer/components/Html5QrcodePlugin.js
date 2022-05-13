import { useEffect, useState } from 'react'
import { Html5Qrcode, Html5QrcodeSupportedFormats } from 'html5-qrcode'

const elementId = 'html5qr-code'

export default function Html5QrcodePlugin(props) {
    useEffect(async function () {
        const config = createConfig(props)
        const verbose = props.verbose === true

        const [device] = await Html5Qrcode.getCameras()

        const html5QrCodeScanner = new Html5Qrcode(elementId, verbose)

        html5QrCodeScanner.start(
            device.id,
            config,
            props.onSuccessCallback,
            props.onErrorCallback
        )

        return () => {
            html5QrCodeScanner.clear()
        }
    }, [])

    return <div id={elementId} style={props.style}></div>
}

function createConfig(props) {
    const config = {
        formatToSupports: [ Html5QrcodeSupportedFormats.QR_CODE ]
    }

    if (props.fps) config.fps = props.fps
    if (props.qrbox) config.qrbox = props.qrbox
    if (props.aspectRatio) config.aspectRatio = props.aspectRatio
    if (props.disableFlip !== undefined) config.disableFlip = props.disableFlip
    
    return config
}