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
import useLocalization from '../../hooks/useLocalization'

import ArrowLeftIcon from '../../icons/ArrowLeftIcon'
import { fetchBranch } from '../../api/branch'
import { fetchServiceById } from '../../api/services'

function useForceUpdate(){
    const [value, setValue] = useState(0)
    return () => setValue(value => value + 1)
}

const validator = new Validator()

function AppointmentOnsiteVisitorInformation() {
    const {t, locale} = useLocalization();
    const PAGE_TITLE = t('Visitor Information')
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
    const [agent, setAgent] = useState('')
    const [vaccine, setVaccine] = useState('')
    const [email, setEmail] = useState('')
    const [contractNumber, setContractNumber] = useState('')
    const [reasonForVisit, setReasonForVisit] = useState('')
    const [headerErrorMessage, setHeaderErrorMessage] = useState(t('Failed to Create Queue'))
    const [errorMessage, setErrorMessage] = useState('')
    const [selectedButton, setSelectedButton] = useState('submit')

    let branch = null
    let service = null
    let bookingFormService = null

    const branchQuery = useQuery(['branch', branchId], () => fetchBranch(branchId))
    const serviceQuery = useQuery(['service', serviceId], () => fetchServiceById(serviceId, { queueType: '', date: '' }))
    
    if (branchQuery.status === 'success') {
        branch = branchQuery.data
    }
    if (serviceQuery.status === 'success') {
        service = serviceQuery.data
    }
    const selectedTemplateForm = service?.template_form_booking ?? branch?.branch_configuration.template_booking_form;
    const validationMessage = {
        name: validator.message('name', name, ['required']),
        phone: validator.message('phone', phone, ['required', 'phone']),
        email: validator.message('email', email, ['required', 'email']),
        ...(selectedTemplateForm === 'form-medical-1' && {
            dateOfBirth: validator.message('dateOfBirth', dateOfBirth, []),
            address: validator.message('address', address, []),
            emergencyNumber: validator.message('emergencyNumber', emergencyNumber, ['phone']),
            passportNumber: validator.message('passportNumber', passportNumber, ['passportNumber']),
            reasonForVisit: validator.message('reasonForVisit', reasonForVisit, ['required']),
        }),
        ...(selectedTemplateForm === 'form-financing' && {
            contractNumber: validator.message('contractNumber', contractNumber, ['required','contractNumber']),
            email: validator.message('email', email, ['email']),
        }),
        ...(selectedTemplateForm === 'form-medical-2' && {
            dateOfBirth: validator.message('dateOfBirth', dateOfBirth, []),
            reasonForVisit: validator.message('reasonForVisit', reasonForVisit, ['required']),
            passportNumber: validator.message('passportNumber', passportNumber, ['required','passportNumber2']),
            email: validator.message('email', email, ['email']),
        }),
        ...(selectedTemplateForm === 'form-medical-3' && {
            dateOfBirth: validator.message('dateOfBirth', dateOfBirth, []),
            passportNumber: validator.message('passportNumber', passportNumber, ['required','passportNumber2']),
            agent: validator.message('agent', agent, ['required']),
        }),
        ...(selectedTemplateForm === 'form-medical-4' && {
            dateOfBirth: validator.message('dateOfBirth', dateOfBirth, []),
            passportNumber: validator.message('passportNumber', passportNumber, ['required','passportNumber2']),
            email: validator.message('email', email, ['email']),
        }),
        ...(selectedTemplateForm === 'form-medical-5' && {
            dateOfBirth: validator.message('dateOfBirth', dateOfBirth, []),
            passportNumber: validator.message('passportNumber', passportNumber, ['required','passportNumber2']),
            email: validator.message('email', email, ['email']),
            vaccine: validator.message('vaccine', vaccine, ['required']),
        }),
    };

    const bookingMutation = useMutation('booking', (data) => createAppointmentOnsite(data))

    const renderStandardUI = () => (
           <div style={{ flex: '1 1 0%' }}>
                        <TextField
                            label={t("Full Name")}
                            style={{ marginBottom: '1.5rem' }}
                            value={name}
                            onChange={(e) => setName(e.target.value)}
                            placeholder={t("Your name")}
                            error={!!validationMessage.name}
                            helperText={t(validationMessage.name)}
                        />
                        <TextField
                            label="Email"
                            type="email"
                            style={{ marginBottom: '1.5rem' }}
                            value={email}
                            onChange={(e) => setEmail(e.target.value)}
                            placeholder={t("Your E-mail")}
                            error={!!validationMessage.email}
                            helperText={t(validationMessage.email)}
                        />
                        <TextField
                            label={t("Phone Number")}
                            type="tel"
                            style={{ marginBottom: '1.5rem' }}
                            value={phone}
                            onChange={(e) => setPhone(e.target.value)}
                            placeholder="+62"
                            error={!!validationMessage.phone}
                            helperText={t(validationMessage.phone)}
                        />
            </div>
    );

    const renderFinanceUI = () => (
    <div style={{ flex: '1 1 0%' }}>
        <TextField
            label={t("Name")}
            style={{ marginBottom: '1.5rem' }}
            value={name}
            onChange={(e) => setName(e.target.value)}
            placeholder={t("Your name")}
            error={!!validationMessage.name}
            helperText={t(validationMessage.name)}
        />
        <TextField
            label="Email"
            type="email"
            style={{ marginBottom: '1.5rem' }}
            value={email}
            onChange={(e) => setEmail(e.target.value)}
            placeholder={t("Your E-mail")}
            error={!!validationMessage.email}
            helperText={t(validationMessage.email)}
        />
        <TextField
            label={t("Contract Number")}
            style={{ marginBottom: '1.5rem' }}
            value={contractNumber}
            onChange={(e) => setContractNumber(e.target.value)}
            placeholder="1234567890"
            error={!!validationMessage.contractNumber}
            helperText={t(validationMessage.contractNumber)}
        />
        <TextField
            label={t("Phone Number")}
            type="tel"
            style={{ marginBottom: '1.5rem' }}
            value={phone}
            onChange={(e) => setPhone(e.target.value)}
            placeholder="+62"
            error={!!validationMessage.phone}
            helperText={t(validationMessage.phone)}
        />
    </div>
    );

    const renderMedicalUI = () => (
        <div style={{ flex: '1 1 0%' }}>
                <TextField
                    label={t("Name")}
                    style={{ marginBottom: '1.5rem' }}
                    value={name}
                    onChange={(e) => setName(e.target.value)}
                    placeholder={t("Your name")}
                    error={!!validationMessage.name}
                    helperText={t(validationMessage.name)}
                />
                <TextField
                    label={t("Date of Birth")}
                    type="date"
                    style={{ marginBottom: '1.5rem' }}
                    value={dateOfBirth}
                    onChange={(e) => setDateOfBirth(e.target.value)}
                    error={!!validationMessage.dateOfBirth}
                    helperText={t(validationMessage.dateOfBirth)}
                />
                <TextField
                    label={t("Address")}
                    style={{ marginBottom: '1.5rem' }}
                    value={address}
                    onChange={(e) => setAddress(e.target.value)}
                    placeholder={t("Your Address")}
                    error={!!validationMessage.address}
                    helperText={t(validationMessage.address)}
                />
                <TextField
                    label={t("Phone Number")}
                    type="tel"
                    style={{ marginBottom: '1.5rem' }}
                    value={phone}
                    onChange={(e) => setPhone(e.target.value)}
                    placeholder="+62"
                    error={!!validationMessage.phone}
                    helperText={t(validationMessage.phone)}
                />
                <TextField
                    label={t("Emergency Number")}
                    type="tel"
                    style={{ marginBottom: '1.5rem' }}
                    value={emergencyNumber}
                    onChange={(e) => setEmergencyNumber(e.target.value)}
                    placeholder="+62"
                    error={!!validationMessage.emergencyNumber}
                    helperText={t(validationMessage.emergencyNumber)}
                />
                <TextField
                    label={t("Passport Number")}
                    style={{ marginBottom: '1.5rem' }}
                    value={passportNumber}
                    onChange={(e) => setPassportNumber(e.target.value)}
                    placeholder="NIK"
                    error={!!validationMessage.passportNumber}
                    helperText={t(validationMessage.passportNumber)}
                />
                <TextField
                    label="Email"
                    type="email"
                    style={{ marginBottom: '1.5rem' }}
                    value={email}
                    onChange={(e) => setEmail(e.target.value)}
                    placeholder={t("Your E-mail")}
                    error={!!validationMessage.email}
                    helperText={t(validationMessage.email)}
                />
                <TextField
                    label={t("Reason for Visit")}
                    style={{ marginBottom: '1.5rem' }}
                    value={reasonForVisit}
                    onChange={(e) => setReasonForVisit(e.target.value)}
                    placeholder="CheckUp"
                    error={!!validationMessage.reasonForVisit}
                    helperText={t(validationMessage.reasonForVisit)}
                />
            </div>
    );
    
    const renderMedicalUI2 = () => (
        <div style={{ flex: '1 1 0%' }}>
                <TextField
                    label={t("Name")}
                    style={{ marginBottom: '1.5rem' }}
                    value={name}
                    onChange={(e) => setName(e.target.value)}
                    placeholder={t("Your name")}
                    error={!!validationMessage.name}
                    helperText={t(validationMessage.name)}
                />
                <TextField
                    label={t("Date of Birth")}
                    type="date"
                    style={{ marginBottom: '1.5rem' }}
                    value={dateOfBirth}
                    onChange={(e) => setDateOfBirth(e.target.value)}
                    error={!!validationMessage.dateOfBirth}
                    helperText={t(validationMessage.dateOfBirth)}
                />
                <TextField
                    label={t("Passport Number")}
                    style={{ marginBottom: '1.5rem' }}
                    value={passportNumber}
                    onChange={(e) => setPassportNumber(e.target.value)}
                    placeholder="NIK"
                    error={!!validationMessage.passportNumber}
                    helperText={t(validationMessage.passportNumber)}
                />
                <TextField
                    label={t("Phone Number")}
                    type="tel"
                    style={{ marginBottom: '1.5rem' }}
                    value={phone}
                    onChange={(e) => setPhone(e.target.value)}
                    placeholder="+62"
                    error={!!validationMessage.phone}
                    helperText={t(validationMessage.phone)}
                />
                <TextField
                    label={t("Reason for Visit")}
                    style={{ marginBottom: '1.5rem' }}
                    value={reasonForVisit}
                    onChange={(e) => setReasonForVisit(e.target.value)}
                    placeholder="CheckUp"
                    error={!!validationMessage.reasonForVisit}
                    helperText={t(validationMessage.reasonForVisit)}
                />
            </div>
    );
    const renderMedicalUI3 = () => (
        <div style={{ flex: '1 1 0%' }}>
                <TextField
                    label={t("Name")}
                    style={{ marginBottom: '1.5rem' }}
                    value={name}
                    onChange={(e) => setName(e.target.value)}
                    placeholder={t("Your name")}
                    error={!!validationMessage.name}
                    helperText={t(validationMessage.name)}
                />
                <TextField
                    label={t("Date of Birth")}
                    type="date"
                    style={{ marginBottom: '1.5rem' }}
                    value={dateOfBirth}
                    onChange={(e) => setDateOfBirth(e.target.value)}
                    error={!!validationMessage.dateOfBirth}
                    helperText={t(validationMessage.dateOfBirth)}
                />
                <TextField
                    label={t("Passport Number")}
                    style={{ marginBottom: '1.5rem' }}
                    value={passportNumber}
                    onChange={(e) => setPassportNumber(e.target.value)}
                    placeholder="NIK"
                    error={!!validationMessage.passportNumber}
                    helperText={t(validationMessage.passportNumber)}
                />
                <TextField
                    label={t("Phone Number")}
                    type="tel"
                    style={{ marginBottom: '1.5rem' }}
                    value={phone}
                    onChange={(e) => setPhone(e.target.value)}
                    placeholder="+62"
                    error={!!validationMessage.phone}
                    helperText={t(validationMessage.phone)}
                />
                 <TextField
                    label="Email"
                    type="email"
                    style={{ marginBottom: '1.5rem' }}
                    value={email}
                    onChange={(e) => setEmail(e.target.value)}
                    placeholder={t("Your E-mail")}
                    error={!!validationMessage.email}
                    helperText={t(validationMessage.email)}
                />
                 <TextField
                    label="Vessel/Agent"
                    style={{ marginBottom: '1.5rem' }}
                    value={agent}
                    onChange={(e) => setAgent(e.target.value)}
                    placeholder={t("Your Agent/Vessel")}
                    error={!!validationMessage.agent}
                    helperText={t(validationMessage.agent)}
                />
            </div>
    );
    const renderMedicalUI4 = () => (
        <div style={{ flex: '1 1 0%' }}>
                <TextField
                    label={t("Name")}
                    style={{ marginBottom: '1.5rem' }}
                    value={name}
                    onChange={(e) => setName(e.target.value)}
                    placeholder={t("Your name")}
                    error={!!validationMessage.name}
                    helperText={t(validationMessage.name)}
                />
                <TextField
                    label={t("Date of Birth")}
                    type="date"
                    style={{ marginBottom: '1.5rem' }}
                    value={dateOfBirth}
                    onChange={(e) => setDateOfBirth(e.target.value)}
                    error={!!validationMessage.dateOfBirth}
                    helperText={t(validationMessage.dateOfBirth)}
                />
                <TextField
                    label={t("Passport Number")}
                    style={{ marginBottom: '1.5rem' }}
                    value={passportNumber}
                    onChange={(e) => setPassportNumber(e.target.value)}
                    placeholder="NIK"
                    error={!!validationMessage.passportNumber}
                    helperText={t(validationMessage.passportNumber)}
                />
                <TextField
                    label={t("Phone Number")}
                    type="tel"
                    style={{ marginBottom: '1.5rem' }}
                    value={phone}
                    onChange={(e) => setPhone(e.target.value)}
                    placeholder="+62"
                    error={!!validationMessage.phone}
                    helperText={t(validationMessage.phone)}
                />
            </div>
    );
    const renderMedicalUI5 = () => (
        <div style={{ flex: '1 1 0%' }}>
                <TextField
                    label={t("Name")}
                    style={{ marginBottom: '1.5rem' }}
                    value={name}
                    onChange={(e) => setName(e.target.value)}
                    placeholder={t("Your name")}
                    error={!!validationMessage.name}
                    helperText={t(validationMessage.name)}
                />
                <TextField
                    label={t("Date of Birth")}
                    type="date"
                    style={{ marginBottom: '1.5rem' }}
                    value={dateOfBirth}
                    onChange={(e) => setDateOfBirth(e.target.value)}
                    error={!!validationMessage.dateOfBirth}
                    helperText={t(validationMessage.dateOfBirth)}
                />
                <TextField
                    label={t("Passport Number")}
                    style={{ marginBottom: '1.5rem' }}
                    value={passportNumber}
                    onChange={(e) => setPassportNumber(e.target.value)}
                    placeholder="NIK"
                    error={!!validationMessage.passportNumber}
                    helperText={t(validationMessage.passportNumber)}
                />
                <TextField
                    label={t("Phone Number")}
                    type="tel"
                    style={{ marginBottom: '1.5rem' }}
                    value={phone}
                    onChange={(e) => setPhone(e.target.value)}
                    placeholder="+62"
                    error={!!validationMessage.phone}
                    helperText={t(validationMessage.phone)}
                />
                <TextField
                    label={t("Vaccine Type")}
                    style={{ marginBottom: '1.5rem' }}
                    value={vaccine}
                    onChange={(e) => setVaccine(e.target.value)}
                    error={!!validationMessage.vaccine}
                    helperText={t(validationMessage.vaccine)}
                />
            </div>
    );

    const renderForm = () => {
        let bookingFormService = serviceQuery.data?.template_form_booking;
        if (serviceQuery.isLoading) {
            return <p>Loading...</p>; 
        }
        if(bookingFormService == null){
            switch (branch?.branch_configuration.template_booking_form) {
                case 'standard-form':
                    return renderStandardUI();
                case 'form-medical-1':
                    return renderMedicalUI();
                case 'form-medical-2':
                    return renderMedicalUI2();
                case 'form-medical-3':
                    return renderMedicalUI3();
                case 'form-medical-4':
                    return renderMedicalUI4();
                case 'form-medical-5':
                    return renderMedicalUI5();
                case 'form-financing':
                    return renderFinanceUI();
                default:
                    return null;
            }
        }else{
            switch (bookingFormService) {
                case 'standard-form':
                    return renderStandardUI();
                case 'form-medical-1':
                    return renderMedicalUI();
                case 'form-medical-2':
                    return renderMedicalUI2();
                case 'form-medical-3':
                    return renderMedicalUI3();
                case 'form-medical-4':
                    return renderMedicalUI4();
                case 'form-medical-5':
                    return renderMedicalUI5();
                case 'form-financing':
                    return renderFinanceUI();
                default:
                    return null;
            }
        }
    };

    async function handleFormSubmit(e) {
        e.preventDefault()

        if (selectedButton === 'submit' && !validator.isAllValid()) {
            validator.showMessages()
            forceUpdate()

            return
        }
        const bookingFormService = serviceQuery.data?.template_form_booking || branch?.branch_configuration.template_booking_form;
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
                ...(bookingFormService === 'form-medical-1' && {
                    address,
                    date_of_birth: dateOfBirth,
                    emergency_number: emergencyNumber,
                    passport_number: passportNumber,
                    reason_for_visit: reasonForVisit,
                }),
                ...(bookingFormService === 'form-medical-2' && {
                    date_of_birth: dateOfBirth,
                    passport_number: passportNumber,
                    reason_for_visit: reasonForVisit,
                }),
                ...(bookingFormService === 'form-medical-3' && {
                    date_of_birth: dateOfBirth,
                    passport_number: passportNumber,
                    agent: agent,
                }),
                ...(bookingFormService === 'form-medical-4' && {
                    date_of_birth: dateOfBirth,
                    passport_number: passportNumber,
                }),
                ...(bookingFormService === 'form-medical-5' && {
                    date_of_birth: dateOfBirth,
                    passport_number: passportNumber,
                    vaccine: vaccine,
                }),
                ...(bookingFormService === 'form-financing' && {
                    contract_number: contractNumber
                })
            })

            if (!booking.success) {
                if(booking.code === 10002) {
                    setHeaderErrorMessage('Connection error')
                } else {
                    setHeaderErrorMessage(t('Failed to Create Queue'))
                }
                showError(booking.message)
                console.log(booking)
                return
            }
            navigate(`/customer/${branchId}/appointment-onsite/booking-status/${booking.data.id}`)
        } catch (error) {
            if(error.code === 10002) {
                setHeaderErrorMessage('Connection error')
            } else {
                setHeaderErrorMessage(t('Failed to Create Queue'))
            }
            console.log(error)
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

                {renderForm()}
                
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
                }} onClick={() => setSelectedButton('submit')}>{t('Next')}</Button>
            </div>
        </form>
    </>
}

export default AppointmentOnsiteVisitorInformation;
