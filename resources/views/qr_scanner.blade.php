<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta
            name="description"
            content="Kyoo is a web app for ordering queue ticket"
        />
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Kyoo</title>

        <link rel="icon" href="{{ asset('img/favico.png') }}" type="image/icon type">

        <style>
            :root {
                --doc-height: 100%;
            }

            * {
                margin: 0;
                padding: 0;
            }

            #qr-scanner {
                position: absolute;
                top: 0;
                left: 0;
                bottom: 0;
                right: 0;
            }

            #qr-scanner video {
                width: 100% !important;
                height: 100vh;
                height: var(--doc-height);
                margin-bottom: 0;
            }
        </style>
    </head>

    <body>
        <div id="qr-scanner"></div>

        <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
        <script>
            (async function () {
                // Initialize QR reader
                const devices = await Html5Qrcode.getCameras()

                const html5QrCode = new Html5Qrcode('qr-scanner')

                const qrConfig = {
                    fps: 10,
                    qrbox: { width: 250, height: 250 },
                    formatsToSupport: [ Html5QrcodeSupportedFormats.QR_CODE ],
                    aspectRatio: window.innerWidth / window.innerHeight
                }

                html5QrCode.start(
                    devices[0].id, 
                    qrConfig,
                    (decodedText, decodedResult) => {
                        location.href = decodedText
                    }
                )
                
                // Set QR reader height
                function setDocumentHeight() {
                    const el = document.documentElement
                    el.style.setProperty('--doc-height', `${window.innerHeight}px`)
                }

                setDocumentHeight()
                window.addEventListener('resize', setDocumentHeight)

                function setShadedBoxSize() {
                    const el = document.getElementById('qr-shaded-region')

                    const rightLeftWidth = (window.innerWidth - qrConfig.qrbox.width) / 2

                    el.style.borderLeftWidth = rightLeftWidth + 'px'
                    el.style.borderRightWidth = rightLeftWidth + 'px'
                }

                window.addEventListener('resize', setShadedBoxSize)
            })()
        </script>
    </body>
</html>
