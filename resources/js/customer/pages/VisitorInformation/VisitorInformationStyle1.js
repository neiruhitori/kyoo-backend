import { useState, useEffect } from 'react'
import { useSearchParams, useParams, useNavigate, Link } from 'react-router-dom'
import Validator from '../../utils/validator'
import { useMutation, useQuery } from 'react-query'

import Header from '../../components/Header'
import ProgressStep from '../../components/ProgressStep'
import MainContent from '../../components/MainContent'
import TextField from '../../components/style1/FormInputStyle1'
import Button from '../../components/style1/ButtonStyle1'
import { fetchBranch } from '../../api/branch'

import ArrowLeftIcon from '../../icons/ArrowLeftIcon'
import useLocalization from './../../hooks/useLocalization';

function useForceUpdate(){
    const [value, setValue] = useState(0)
    return () => setValue(value => value + 1)
}

const validator = new Validator()

function VisitorInformationStyle1() {
    const {t} = useLocalization();

    const PAGE_TITLE = t('Booking Appointment')
    const forceUpdate = useForceUpdate()
    const [searchParams] = useSearchParams()
    const { queueType, branchId, serviceId } = useParams()
    const navigate = useNavigate()

    let branch = null
    const branchQuery = useQuery(['branch', branchId], () => fetchBranch(branchId))
    
        if (branchQuery.status === 'success') {
            branch = branchQuery.data
        }

    const [name, setName] = useState('')
    const [phone, setPhone] = useState('')
    const [email, setEmail] = useState('')
    const [notes, setNotes] = useState('')
    const [isFilled, setIsFilled] = useState(false)

    const validationMessage = {
        name: validator.message('name', name, ['required']),
        phone: validator.message('phone', phone, ['required', 'phone']),
        email: validator.message('email', email, ['required', 'email']),
    }

    useEffect(() => {
        if (name && phone && email) {
          setIsFilled(true);
        } else {
          setIsFilled(false);
        }
      }, [name, phone, email]);

    const renderStandardUI = () => (
        <div style={{
            flex: '1 1 0%'
        }}>
            <TextField
                label={t("Full Name")}
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
                label={t("Phone Number")}
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
                label={t("Notes")}
                style={{
                    marginBottom: '1.5rem'
                }}
                value={notes}
                onChange={(e) => setNotes(e.target.value)}
                placeholder={t("Optional")}
            />
        </div>
 );
    const renderMedicalChildUI = () => (
        <div style={{
            flex: '1 1 0%'
        }}>
            <TextField
                label={t("Full Name")}
                style={{
                    marginBottom: '1.5rem'
                }}
                value={name}
                onChange={(e) => setName(e.target.value)}
                placeholder="Isi Nama Lengkap Ananda"
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
                placeholder="Isi Email Orang Tua atau Pendamping"
                error={!!validationMessage.email}
                helperText={validationMessage.email}
            />

            <TextField
                label={t("Phone Number")}
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
                label={t("Notes")}
                style={{
                    marginBottom: '1.5rem'
                }}
                value={notes}
                onChange={(e) => setNotes(e.target.value)}
                placeholder={t("Optional")}
            />
        </div>
 );

 const renderForm = () => {
    let templateForm = branch?.branch_configuration.template_booking_form;
    if(!templateForm){
        return null
    }
    
    switch (templateForm) {
        case 'standard-form':
            return renderStandardUI();
        case 'form-medical-child':
            return renderMedicalChildUI();
        default:
            return renderStandardUI();
    }
};

    function handleFormSubmit(e) {
        e.preventDefault()

        if (validator.isAllValid()) {
            const formData = new URLSearchParams({
                date: searchParams.get('date'),
                slot_id: searchParams.get('slot'),
                name,
                phone,
                email,
                notes
            })

            navigate(`/customer/${branchId}/${queueType}/services/${serviceId}/booking-confirmation?${formData}`)
        } else {
            validator.showMessages()
            forceUpdate()
        }
    }

    return <>
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
                marginLeft: '5rem',
                textTransform: 'capitalize',
                flex: '1 1 0%'
            }}>{PAGE_TITLE}</div>
        </Header>

        <div style={{ padding: '2rem 1.375rem', }}>
            <h2 style={{ marginBottom:'0.5rem' }}>Informasi Pengunjung</h2>
            <p style={{ color:'#8e8e8e' }}>Silahkan isi data diri anda</p>
        </div>

        <form onSubmit={handleFormSubmit} style={{
            display: 'flex',
            flexDirection: 'column',
            flexGrow: '1'
        }}>
            <MainContent style={{
                flex: '1 1 0%',
                height: '100%',
            }}>
              {renderForm()}
            </MainContent>

            <div style={{
                boxShadow: '0px -4px 40px rgba(0, 0, 0, 0.13)',
                borderRadius: '16px 16px 0 0',
                padding: '1.75rem 1.375rem'
            }}>
                <Button 
                color={isFilled ? "primary" : "secondary"}
                type="submit" 
                disabled={!isFilled}
                style={{
                    width: '100%',
                    fontSize: '1rem',
                    color: isFilled ? '#FFFFFF' : '#8e8e8e',
                    backgroundColor: isFilled ? '#0e3776' : '#E8EDF1'
                }}>{t('Next')}</Button>
            </div>
        </form>
    </>
}

export default VisitorInformationStyle1;