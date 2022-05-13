import { useRef, useEffect, useState } from 'react'
import styled from 'styled-components'
import { useMutation } from 'react-query'

import { searchBookingByBookingCode } from '../../api/booking'

import Html5QrcodePlugin from '../../components/Html5QrcodePlugin'
import KyooLogo from '../../components/KyooLogo'
import DangerAlert from '../../components/DangerAlert'

const QRReaderRoot = styled.div`
    min-height: ${window.innerHeight}px;
    overflow: hidden;
    background-color: #04111D;
    position: relative;
    color: #FFFFFF;
`
const SwitchButton = styled.button((props) => {
    return {
        flex: '1 1 0%',
        padding: '1rem',
        textAlign: 'center',
        borderRadius: '8px',
        backgroundColor: props.active ? '#FFFFFF' : 'transparent',
        border: 'none',
        outline: 'none',
        fontSize: '1.125rem',
        color: props.active ? '#000000' : '#FFFFFF'
    }
})

const PageTitle = styled.h4(() => ({
    fontWeight: 700,
    fontSize: '1.5rem',
    textAlign: 'center',
    marginBottom: '1rem'
}))

const initialHeight = window.innerHeight

export default function QRReader() {
    const qrRef = useRef(null)

    const [parentWidth, setParentWidth] = useState(0)
    const [activeSection, setActiveSection] = useState('qr')
    const [bookingCode, setBookingCode] = useState('')
    const [errorMessage, setErrorMessage] = useState('')

    const searchMutation = useMutation('booking', (bookingCode) => searchBookingByBookingCode(bookingCode))

    useEffect(function () {
        if (qrRef.current) {
            setParentWidth(qrRef.current.offsetWidth)
        }
    }, [])

    function onSuccess (decodeText, decodedResult) {
        if (!new RegExp('^http').test(decodeText)) {
            console.log('Invalid response:', decodeText)
            return
        }

        location.href = decodeText
    }

    async function handleSubmit(e) {
        e.preventDefault()

        try {
            const response = await searchMutation.mutateAsync(bookingCode)

            if (response.success) {
                location.href = response.data.url
                return
            }

            showMessage(response.message)
        } catch (err) {
            showMessage(err.message)
        }
    }

    function showMessage(message) {
        setErrorMessage(message)

        setTimeout(function () {
            setErrorMessage('')
        }, 5000)
    }

    function getAspectRatio() {
        if (screen.width > screen.height) {
            return parentWidth / initialHeight
        }

        return initialHeight / parentWidth
    }


    return <QRReaderRoot ref={qrRef}>
        <style>
            {`
                #html5qr-code {
                    top: ${window.innerHeight / 2}px;
                    transform: translateY(-50%);
                }
            `}
        </style>

        <div style={{
            padding: '0 1.5rem',
            position: 'absolute',
            top: 0, right: 0, bottom: 0, left: 0,
            zIndex: 9999,
            display: 'flex',
            flexDirection: 'column'
        }}>
            <div style={{
                margin: '2rem 0',
                textAlign: 'center'
            }}>
                <KyooLogo width="2.5rem" />
            </div>

            {!!errorMessage && <DangerAlert style={{
                width: 'max-content',
                margin: '0 auto',
                position: 'fixed',
                top: '94px',
                left: '50%',
                transform: 'translateX(-50%)',
                zIndex: 9999999999
            }}>{errorMessage}</DangerAlert>}

            {activeSection === 'qr' && <div style={{
                position: 'absolute',
                top: (initialHeight - 440) / 2,
                left: '50%',
                transform: 'translateX(-50%)',
                width: '260px'
            }}>
                <PageTitle>Scan Kode QR</PageTitle>
                <p style={{
                    textAlign: 'center'
                }}>Sejajarkan Kode QR dengan kotak dibawah</p>
            </div>}

            {activeSection === 'code' && <div style={{
                display: 'flex',
                flexDirection: 'column',
                justifyContent: 'center',
                flex: '1 1 0%'
            }}>
                <div style={{
                    width: '260px',
                    margin: '0 auto',
                    marginBottom: '2.25rem'
                }}>
                    <PageTitle>Masukkan Kode Unik</PageTitle>
                    <p style={{
                        textAlign: 'center'
                    }}>Masukkan kode unik untuk melihat status antrian Anda</p>
                </div>

                <div style={{
                    backgroundColor: '#FFFFFF',
                    borderRadius: '8px'
                }}>
                    <form onSubmit={handleSubmit} style={{
                        display: 'flex'
                    }}>
                        <div style={{
                            flex: '1 1 0%',
                            color: '#000000',
                            overflow: 'hidden',
                            padding: '.75rem'
                        }}>
                            <label htmlFor="bookingCode" style={{
                                textTransform: 'uppercase',
                                marginBottom: '.5rem',
                                letterSpacing: '.02em',
                                fontSize: '.75rem',
                                display: 'inline-block'
                            }}>Kode Unik</label>

                            <input
                                type="text"
                                name="booking_code"
                                id="bookingCode"
                                placeholder="Eg. A7h6x8"
                                value={bookingCode}
                                onChange={(e) => setBookingCode(e.target.value)}
                                style={{
                                    display: 'block',
                                    fontSize: '1.125rem',
                                    fontFamily: 'Inter, sans-serif',
                                    border: 'none',
                                    outline: 'none',
                                    width: '100%'
                                }}
                            />
                        </div>
                        
                        <div style={{
                            padding: '.75rem',
                            paddingLeft: 0
                        }}>
                            <button type="submit" style={{
                                backgroundColor: '#103C7C',
                                padding: '1rem',
                                borderRadius: '8px',
                                color: '#FFFFFF',
                                border: 'none',
                                outline: 'none',
                                width: '112px',
                                display: 'inline-block',
                                fontSize: '1.125rem'
                            }}>
                                Cari
                            </button>
                        </div>
                    </form>
                </div>
            </div>}

            <div style={{
                margin: 'auto 0 2rem 0',
                justifySelf: 'self-end',
                backgroundColor: 'rgba(255, 255, 255, .08)',
                borderRadius: '16px',
                padding: '.75rem',
                display: 'flex',
                color: 'black',
                backdropFilter: 'blur(16px)'
            }}>
                <SwitchButton
                    active={activeSection === 'qr'}
                    onClick={() => {
                        setActiveSection('qr')
                    }}
                >Scan QR</SwitchButton>

                <SwitchButton
                    active={activeSection === 'code'}
                    onClick={() => {
                        setActiveSection('code')
                    }}
                >Kode Unik</SwitchButton>
            </div>
        </div>

        {activeSection === 'qr' && (parentWidth > 0) && <Html5QrcodePlugin
            fps={10}
            qrbox={225}
            aspectRatio={getAspectRatio()}
            onSuccessCallback={onSuccess}
        />}
    </QRReaderRoot>
}