import { useState } from 'react'
import { useSearchParams, useParams, useNavigate, Link } from 'react-router-dom'
import Validator from '../../utils/validator'

import Header from '../../components/Header'
import ProgressStep from '../../components/ProgressStep'
import MainContent from '../../components/MainContent'
import TextField from '../../components/TextField'
import Button from '../../components/Button'

import ArrowLeftIcon from '../../icons/ArrowLeftIcon'

function useForceUpdate(){
    const [value, setValue] = useState(0)
    return () => setValue(value => value + 1)
}

const validator = new Validator()

function VisitorInformation() {
    const PAGE_TITLE = 'Informasi Pengunjung'
    const forceUpdate = useForceUpdate()
    const [searchParams] = useSearchParams()
    const { queueType, branchId, serviceId } = useParams()
    const navigate = useNavigate()

    const [name, setName] = useState('')
    const [phone, setPhone] = useState('')
    const [email, setEmail] = useState('')
    const [notes, setNotes] = useState('')

    const validationMessage = {
        name: validator.message('name', name, ['required']),
        phone: validator.message('phone', phone, ['required', 'phone']),
        email: validator.message('email', email, ['required', 'email']),
    }

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

            navigate(`/kyooTicket/${queueType}/${branchId}/services/${serviceId}/booking-confirmation?${formData}`)
        } else {
            validator.showMessages()
            forceUpdate()
        }
    }

    return <>
        <Header style={{
            justifyContent: 'space-between'
        }}>
            <div style={{
                flex: '1 1 0%',
                display: 'flex'
            }}>
                <div style={{
                    marginRight:' 0.75rem'
                }}>
                    <Link to={-1} style={{
                        display: 'flex',
                        justifyContent: 'center',
                        alignItems: 'center'
                    }}>
                        <ArrowLeftIcon />
                    </Link>
                </div>

                <div style={{ textTransform: 'capitalize' }}>{PAGE_TITLE}</div>
            </div>
        
            <div style={{
                width: '60px'
            }}>
                <ProgressStep active="1" total="3" />
            </div>
        </Header>

        <MainContent style={{
            flex: '1 1 0%',
            height: '100%',
        }}>
            <form onSubmit={handleFormSubmit} style={{
                display: 'flex',
                flexDirection: 'column',
                flexGrow: '1'
            }}>
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

                    <TextField
                        label="Catatan"
                        style={{
                            marginBottom: '1.5rem'
                        }}
                        value={notes}
                        onChange={(e) => setNotes(e.target.value)}
                        placeholder="Opsional"
                    />
                </div>

                <div style={{
                    boxShadow: '0px -4px 40px rgba(0, 0, 0, 0.13)',
                    borderRadius: '16px 16px 0 0',
                    padding: '1.75rem 1.375rem',
                    margin: '0 -1.375rem'
                }}>
                    <Button color="primary" type="submit" style={{
                        width: '100%',
                        fontSize: '1rem'
                    }}>Selanjutnya</Button>
                </div>
            </form>
        </MainContent>
    </>
}

export default VisitorInformation;