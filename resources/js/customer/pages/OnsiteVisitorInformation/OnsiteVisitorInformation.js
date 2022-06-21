import { useState } from 'react'
import { useMutation } from 'react-query'
import { useParams, useNavigate, Link } from 'react-router-dom'

import { useToken, useMessaging } from '../../lib/firebase'
import Validator from '../../utils/validator'
import { createBooking } from '../../api/booking'

import Header from '../../components/Header'
import MainContent from '../../components/MainContent'
import TextField from '../../components/TextField'
import Button from '../../components/Button'
import DangerAlert from '../../components/DangerAlert'
import Loading from '../../components/Loading'

import ArrowLeftIcon from '../../icons/ArrowLeftIcon'

function useForceUpdate(){
    const [value, setValue] = useState(0)
    return () => setValue(value => value + 1)
}

const validator = new Validator()

function VisitorInformation() {
    const PAGE_TITLE = 'Informasi Pengunjung'
    const forceUpdate = useForceUpdate()
    const { branchId, serviceId } = useParams()
    const navigate = useNavigate()

    const messaging = useMessaging()
    const fcm_id = useToken(messaging, process.env.MIX_FIREBASE_VAPID_KEY)

    const [name, setName] = useState('')
    const [phone, setPhone] = useState('')
    const [email, setEmail] = useState('')
    const [errorMessage, setErrorMessage] = useState('')
    const [selectedButton, setSelectedButton] = useState('submit')

    const validationMessage = {
        name: validator.message('name', name, ['required']),
        phone: validator.message('phone', phone, ['required', 'phone']),
        email: validator.message('email', email, ['required', 'email']),
    }

    const bookingMutation = useMutation('booking', (data) => createBooking('onsite', data))

    async function handleFormSubmit(e) {
        e.preventDefault()

        if (selectedButton === 'submit' && !validator.isAllValid()) {
            validator.showMessages()
            forceUpdate()

            return   
        }

        try {
            const booking = await bookingMutation.mutateAsync({
                service_id: serviceId,
                name,
                phone,
                email,
                fcm_id
            })

            if (!booking.success) {
                showError(booking.message)
                return
            }

            navigate(`/customer/${branchId}/onsite/booking-status/${booking.data.id}`)
        } catch (error) {
            showError(error.message)
        }
    }

    function showError(message) {
        setErrorMessage(message)
    }

    return <>
        {bookingMutation.status === 'loading' && <Loading />}
        
        <Header>
            <div style={{
                height: '3.2rem',
                display: 'flex'
            }}>
                <Link to={-1} style={{
                    display: 'flex',
                    justifyContent: 'center',
                    alignItems: 'center',
                    padding: '.85rem 1.375rem'
                }}>
                    <ArrowLeftIcon />
                </Link>
            </div>

            <div style={{
                textTransform: 'capitalize',
                flex: '1 1 0%'
            }}>{PAGE_TITLE}</div>
        </Header>

        <form onSubmit={handleFormSubmit} style={{
            display: 'flex',
            flexDirection: 'column',
            flexGrow: '1'
        }}>
            <MainContent style={{
                flex: '1 1 0%',
                height: '100%',
            }}>
                {!!errorMessage && <DangerAlert style={{
                    marginBottom: '1rem'
                }}>
                    <h4 style={{
                        fontSize: '1rem',
                        marginBottom: '.375rem',
                        textTransform: 'capitalize'
                    }}>Gagal membuat antrian</h4>

                    <p style={{
                        lineHeight: '1.5',
                    }}>
                        {errorMessage}
                    </p>
                </DangerAlert>}

            
                <div style={{
                    flex: '1 1 0%'
                }}>
                    <TextField
                        label="Nama Lengkap"
                        style={{
                            marginBottom: '1.5rem'
                        }}
                        value={name}
                        onChange={(e) => setName(e.target.value)}
                        placeholder="Ch. John Doe"
                        error={!!validationMessage.name}
                        helperText={validationMessage.name}
                    />

                    <TextField
                        label="Email"
                        type="email"
                        style={{
                            marginBottom: '1.5rem'
                        }}
                        value={email}
                        onChange={(e) => setEmail(e.target.value)}
                        placeholder="Ch. john@mail.com"
                        error={!!validationMessage.email}
                        helperText={validationMessage.email}
                    />

                    <TextField
                        label="No. Telepon"
                        type="tel"
                        style={{
                            marginBottom: '1.5rem'
                        }}
                        value={phone}
                        onChange={(e) => setPhone(e.target.value)}
                        placeholder="+62"
                        error={!!validationMessage.phone}
                        helperText={validationMessage.phone}
                    />
                </div>
            </MainContent>

            <div style={{
                boxShadow: '0px -4px 40px rgba(0, 0, 0, 0.13)',
                borderRadius: '16px 16px 0 0',
                padding: '1.75rem 1.375rem'
            }}>
                <Button color="primary" type="submit" style={{
                    width: '100%',
                    fontSize: '1rem',
                    marginBottom: '.5rem'
                }} onClick={() => setSelectedButton('submit')}>Selanjutnya</Button>

                <Button color="link" type="submit" style={{
                    width: '100%',
                    fontSize: '1rem'
                }} onClick={() => setSelectedButton('skip')}>Lewati</Button>
            </div>
        </form>
    </>
}

export default VisitorInformation;