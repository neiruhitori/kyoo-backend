import { useParams, useSearchParams, Link } from 'react-router-dom'
import { useQuery, useMutation } from 'react-query'
import { format, formatBrowser } from '../../utils/date'

import Header from '../../components/Header'
import MainContent from '../../components/MainContent'
import Card from '../../components/Card'
import ProgressStep from '../../components/ProgressStep'
import Button from '../../components/style1/ButtonStyle1'
import Loading from '../../components/Loading'
import Tooltip from '../../components/Tooltip'
import TooltipContent from '../../components/TooltipContent'
import DangerAlert from '../../components/DangerAlert'

import ArrowLeftIcon from '../../icons/ArrowLeftIcon'
import { fetchBranch } from '../../api/branch'
import { fetchServiceById } from '../../api/services'
import { createBooking } from '../../api/booking'
import useLocalization from '../../hooks/useLocalization'
import BoxOpenIcon from './../../icons/BoxOpenIcon';
import UserIcon from '../../icons/UserIcon'

function BookingConfirmation() {
    const{t, locale} = useLocalization();
    const { branchId, serviceId, queueType } = useParams()
    const [searchParams] = useSearchParams()
    const PAGE_TITLE = t(`Queue Confirmation`)

    const branchRes = useQuery('branch', () => fetchBranch(branchId))
    const serviceRes = useQuery('service', () => fetchServiceById(serviceId, {
        queueType,
        date: searchParams.get('date')
    }))
    const bookingMutation = useMutation('booking', (data) => createBooking(queueType, data))

    let branch = null
    let service = null
    let slot = null
    let booking = null

    if (branchRes.status === 'success') {
        branch = branchRes.data
    }

    if (serviceRes.status === 'success') {
        service = serviceRes.data
        slot = service.slot.find(v => {
            return v.id == searchParams.get('slot_id')
        })
    }

    if (bookingMutation.status === 'success') {
        booking = bookingMutation.data?.data
    }

    function handleClick() {
        bookingMutation.mutate({
            slot_id: searchParams.get('slot_id'),
            date: searchParams.get('date'),
            name: searchParams.get('name'),
            phone: searchParams.get('phone'),
            email: searchParams.get('email'),
            notes: searchParams.get('notes')
        })
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
                marginLeft: '6rem',
                textTransform: 'capitalize',
                flex: '1 1 0%'
            }}>{PAGE_TITLE}</div>
        </Header>

        <div style={{ padding: '2rem 1.375rem', }}>
            <h2 style={{ marginBottom:'0.5rem' }}>Konfirmasi Antrian</h2>
            <p style={{ color:'#8e8e8e' }}>Silahkan cek kembali data dan detail booking</p>
        </div>

        <MainContent style={{
            flex: '1 1 0%',
            height: '100%',
        }}>
            <div style={{
                flexGrow: '1'
            }}>
                {bookingMutation.status === 'success' && !bookingMutation.data.success && <DangerAlert style={{
                    marginBottom: '1rem'
                }}>
                    <h4 style={{
                        fontSize: '1rem',
                        marginBottom: '.375rem',
                        textTransform: 'capitalize'
                    }}>{t('Failed to Create Queue')}</h4>

                    <p style={{
                        lineHeight: '1.5',
                    }}>
                        {bookingMutation.data.message}
                    </p>
                </DangerAlert>}

                {bookingMutation.status === 'success' && bookingMutation.data.success && <Tooltip>
                    <TooltipContent>
                        <p style={{
                            marginBottom: '1rem',
                            lineHeight: '1.5'
                        }}>
                            {t('Your queue has been saved and sent to your email as proof that the queue has been booked')}
                        </p>

                        <Link to={`/customer/${branchId}/${queueType}/booking-status/${booking.id}`} style={{
                            padding: '1rem 1.125rem',
                            backgroundColor: '#ECFBFF',
                            color: '#103C7C',
                            display: 'block',
                            textAlign: 'center',
                            borderRadius: '6px',
                            fontWeight: '500'
                        }}>
                            {t('View Queue Status')}
                        </Link>
                    </TooltipContent>
                </Tooltip>}

                <Card style={{
                    marginBottom: '1.625rem'
                }}>
                    <div style={{ display:'flex', alignItems:'center', marginBottom:'2rem'}}>
                        <div style={{ backgroundColor:'#103C7C',
                                    borderRadius:'6px',
                                    marginRight: '0.75rem',
                                     padding:'0.4rem 0.5rem' }}>
                            <span>
                                <BoxOpenIcon color="#fff" width="30px" height="25px" />
                            </span>
                        </div>
                        <h4>{t('Booking Details')}</h4>
                    </div>

                    {branchRes.status === 'success' && serviceRes.status === 'success' && 
                    <div style={{ display:'flex', justifyContent:'center', gap:'2rem', flexWrap:'wrap' }}>
                        <div style={{
                            display: 'flex',
                            flexDirection:'column',
                            flex:'1 1 160px',
                            maxWidth:'240px',
                            minWidth:'160px',
                            wordWrap:'break-word',
                            gap:'1rem',
                            fontSize: '.875rem',
                            marginBottom: '1.125rem'
                        }}>
                            <div>
                                    <div style={{
                                        color: '#A5A5A5',
                                        marginBottom:'0.4rem'
                                    }}>{t('Booking Date')}</div>

                                    <div style={{
                                        color: '#103C7C',
                                        fontWeight: '600'
                                    }}>
                                        <h3>
                                            {format(formatBrowser(searchParams.get('date')),locale)}
                                        </h3>
                                </div>
                            </div>

                            <div>
                                    <div style={{
                                        color: '#A5A5A5',
                                        marginBottom:'0.4rem'
                                    }}>{t('Service')}</div>

                                    <div style={{
                                        color: '#103C7C',
                                        fontWeight: '600'
                                    }}>
                                        <h3>
                                            {service.name}
                                        </h3>
                                </div>
                            </div>
                        </div>

                        <div style={{
                            display: 'flex',
                            flexDirection:'column',
                            flex:'1 1 160px',
                            maxWidth:'240px',
                            minWidth:'160px',
                            wordWrap:'break-word',
                            gap:'1rem',
                            fontSize: '.875rem',
                            marginBottom: '1.125rem'
                        }}>

                            <div>
                                <div style={{
                                    color: '#A5A5A5',
                                    marginBottom:'0.4rem'
                                }}>{t('Branch Name')}</div>

                                <div style={{
                                    color: '#103C7C',
                                    fontWeight: '600'
                                }}>
                                    <h3>
                                        {branch.name}
                                    </h3>
                                </div>
                            </div>
                            

                            <div>
                                <div style={{
                                    color: '#A5A5A5',
                                    marginBottom:'0.4rem'
                                }}>{t('Time')}</div>

                                <div style={{
                                    color: '#103C7C',
                                    fontWeight: '600'
                                }}>
                                    <h3>
                                        {slot.start_time} - {slot.end_time}
                                    </h3>
                                </div>
                            </div>
                        </div>
                        {/*  */}
                    </div>}
                </Card>

                <Card style={{
                    marginBottom: '1.625rem'
                }}>
                    <div style={{ display:'flex', alignItems:'center', marginBottom:'2rem' }}>
                        <div style={{ backgroundColor:'#103C7C',
                                    borderRadius:'6px',
                                    marginRight: '0.75rem',
                                     padding:'0.4rem 0.5rem' }}>
                            <span>
                                <UserIcon color="#fff" width="30px" height="25px" />
                            </span>
                        </div>
                        <h4>{t('Customer Details')}</h4>
                    </div>

                    {branchRes.status === 'success' && serviceRes.status === 'success' && 
                    <div style={{ display:'flex', justifyContent:'center', gap:'2rem', flexWrap:'wrap' }}>
                        <div style={{
                            display: 'flex',
                            flexDirection:'column',
                            flex:'1 1 160px',
                            maxWidth:'240px',
                            minWidth:'160px',
                            wordWrap:'break-word',
                            gap:'1rem',
                            fontSize: '.875rem',
                            marginBottom: '1.125rem'
                        }}>
                            <div>
                                    <div style={{
                                        color: '#A5A5A5',
                                        marginBottom:'0.4rem'
                                    }}>{t('Full Name')}</div>

                                    <div style={{
                                        color: '#103C7C',
                                        fontWeight: '600'
                                    }}>
                                        <h3>
                                        {searchParams.get('name')}
                                        </h3>
                                </div>
                            </div>

                            <div>
                                    <div style={{
                                        color: '#A5A5A5',
                                        marginBottom:'0.4rem'
                                    }}>{t('Phone Number')}</div>

                                    <div style={{
                                        color: '#103C7C',
                                        fontWeight: '600'
                                    }}>
                                        <h3>
                                            {searchParams.get('phone')}
                                        </h3>
                                </div>
                            </div>
                        </div>

                        <div style={{
                            display: 'flex',
                            flexDirection:'column',
                            flex:'1 1 160px',
                            maxWidth:'240px',
                            minWidth:'160px',
                            wordWrap:'break-word',
                            gap:'1rem',
                            fontSize: '.875rem',
                            marginBottom: '1.125rem'
                        }}>

                            <div>
                                <div style={{
                                    color: '#A5A5A5',
                                    marginBottom:'0.4rem'
                                }}>{t('Email')}</div>

                                <div style={{
                                    color: '#103C7C',
                                    fontWeight: '600'
                                }}>
                                    <h3>
                                    {searchParams.get('email')}
                                    </h3>
                                </div>
                            </div>
                            

                            <div>
                                <div style={{
                                    color: '#A5A5A5',
                                    marginBottom:'0.4rem'
                                }}>{t('Notes')}</div>

                                <div style={{
                                    color: '#103C7C',
                                    fontWeight: '600'
                                }}>
                                    <h3>
                                        {searchParams.get('notes') || '-'}
                                    </h3>
                                </div>
                            </div>
                        </div>
                        {/*  */}
                    </div>}
                </Card>
                {/*  */}
            </div>
        </MainContent>

        <div style={{
            boxShadow: '0px -4px 40px rgba(0, 0, 0, 0.13)',
            borderRadius: '16px 16px 0 0',
            padding: '1.75rem 1.375rem'
        }}>
            <Link to={-1}>
                <Button color="secondary" style={{
                    width: '100%',
                    fontSize: '1rem',
                    marginBottom: '1rem',
                }}>{t('Cancel')}</Button>
            </Link>

            <Button color="primary" onClick={handleClick} style={{
                width: '100%',
                fontSize: '1rem'
            }}>{t('Book Now')}</Button>
        </div>
    </>
}

export default BookingConfirmation