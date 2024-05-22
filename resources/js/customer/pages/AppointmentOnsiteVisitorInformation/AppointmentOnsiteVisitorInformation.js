import { useState } from 'react'
import { useMutation, useQuery } from 'react-query'
import { useParams, useNavigate, Link, useLocation, useSearchParams } from 'react-router-dom'

import { useToken, useMessaging } from '../../lib/firebase'
import Validator from '../../utils/validator'
import { createAppointmentOnsite } from '../../api/appointmentOnsite'

import Header from '../../components/Header'
import MainContent from '../../components/MainContent'
import TextField from '../../components/TextField'
import Button from '../../components/Button'
import DangerAlert from '../../components/DangerAlert'
import Loading from '../../components/Loading'

import ArrowLeftIcon from '../../icons/ArrowLeftIcon'
import { fetchBranch } from '../../api/branch'

function useForceUpdate(){
    const [value, setValue] = useState(0)
    return () => setValue(value => value + 1)
}

const validator = new Validator()

function AppointmentOnsiteVisitorInformation() {
    const PAGE_TITLE = 'Informasi Pengunjung'
    const forceUpdate = useForceUpdate()
    const [searchParams] = useSearchParams()
    const { branchId, serviceId } = useParams()
    const navigate = useNavigate()

    const messaging = useMessaging()
    const fcm_id = useToken(messaging, process.env.MIX_FIREBASE_VAPID_KEY)

    const [name, setName] = useState('')
    const [dateOfBirth, setDateOfBirth] = useState('')
    const [address, setAddress] = useState('')
    const [phone, setPhone] = useState('')
    const [emergencyNumber, setEmergencyNumber] = useState('')
    const [passportNumber, setPassportNumber] = useState('')
    const [email, setEmail] = useState('')
    const [reasonForVisit, setReasonForVisit] = useState('')
    const [headerErrorMessage, setHeaderErrorMessage] = useState('Gagal membuat antrian')
    const [errorMessage, setErrorMessage] = useState('')
    const [selectedButton, setSelectedButton] = useState('submit')

    let branch = null

    const branchQuery = useQuery(['branch', branchId], () => fetchBranch(branchId))

    if (branchQuery.status === 'success') {
        branch = branchQuery.data
    }

    const validationMessage = {
        name: validator.message('name', name, ['required']),
        phone: validator.message('phone', phone, ['required', 'phone']),
        email: validator.message('email', email, ['required', 'email']),
        ...(branch && branch.branch_configuration.template_booking_form !== 'standard-form' && {
            dateOfBirth: validator.message('dateOfBirth', dateOfBirth, []),
            address: validator.message('address', address, []),
            emergencyNumber: validator.message('emergencyNumber', emergencyNumber, ['phone']),
            passportNumber: validator.message('passportNumber', passportNumber, ['passportNumber']),
            reasonForVisit: validator.message('reasonForVisit', reasonForVisit, ['required']),
        }),
    };

    const bookingMutation = useMutation('booking', (data) => createAppointmentOnsite(data))

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
                branch_id: branchId,
                name,
                phone,
                email,
                fcm_id,
                date: searchParams.get('date'),
                slot_id: searchParams.get('slot'),
                ...(branch && branch.branch_configuration.template_booking_form !== 'standard-form' && {
                    address,
                    date_of_birth: dateOfBirth,
                    emergency_number: emergencyNumber,
                    passport_number: passportNumber,
                    reason_for_visit: reasonForVisit,
                })
            })

            if (!booking.success) {
                if(booking.code === 10002) {
                    setHeaderErrorMessage('Gangguan Koneksi')
                } else {
                    setHeaderErrorMessage('Gagal membuat antrian')
                }
                showError(booking.message)
                return
            }

            navigate(`/customer/${branchId}/appointment-onsite/booking-status/${booking.data.id}`)
        } catch (error) {
            if(error.code === 10002) {
                setHeaderErrorMessage('Gangguan Koneksi')
            } else {
                setHeaderErrorMessage('Gagal membuat antrian')
            }
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
                    }}>{headerErrorMessage}</h4>

                    <p style={{
                        lineHeight: '1.5',
                    }}>
                        {errorMessage}
                    </p>
                </DangerAlert>}


                {branch?.branch_configuration.template_booking_form === 'standard-form' ?
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
                :
                <div style={{
                        flex: '1 1 0%'
                    }}>
                        <TextField
                            label="Nama/Name"
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
                            label="Tanggal lahir/DOB"
                            type="date"
                            style={{
                                marginBottom: '1.5rem'
                            }}
                            value={dateOfBirth}
                            onChange={(e) => setDateOfBirth(e.target.value)}
                            error={!!validationMessage.dateOfBirth}
                            helperText={validationMessage.dateOfBirth}
                        />

                        <TextField
                            label="Alamat/Address"
                            style={{
                                marginBottom: '1.5rem'
                            }}
                            value={address}
                            onChange={(e) => setAddress(e.target.value)}
                            placeholder="Ch. Jln Merdeka"
                            error={!!validationMessage.address}
                            helperText={validationMessage.address}
                        />

                        <TextField
                            label="No. Telepon/Phone number"
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

                        <TextField
                            label="Nomer cadangan/Emergency Number"
                            type="tel"
                            style={{
                                marginBottom: '1.5rem'
                            }}
                            value={emergencyNumber}
                            onChange={(e) => setEmergencyNumber(e.target.value)}
                            placeholder="+62"
                            error={!!validationMessage.emergencyNumber}
                            helperText={validationMessage.emergencyNumber}
                        />

                        <TextField
                            label="NIK/Passport Number"
                            style={{
                                marginBottom: '1.5rem'
                            }}
                            value={passportNumber}
                            onChange={(e) => setPassportNumber(e.target.value)}
                            placeholder="NIK"
                            error={!!validationMessage.passportNumber}
                            helperText={validationMessage.passportNumber}
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
                            label="Alasan Kunjungan/Reason for visit"
                            style={{
                                marginBottom: '1.5rem'
                            }}
                            value={reasonForVisit}
                            onChange={(e) => setReasonForVisit(e.target.value)}
                            placeholder="Ch. CheckUp"
                            error={!!validationMessage.reasonForVisit}
                            helperText={validationMessage.reasonForVisit}
                        />
                    </div>
                }
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
            </div>
        </form>
    </>
}

export default AppointmentOnsiteVisitorInformation;
