import { useParams, useSearchParams, Link } from 'react-router-dom'
import { useQuery, useMutation } from 'react-query'
import { format, formatBrowser } from '../../utils/date'

import Header from '../../components/Header'
import MainContent from '../../components/MainContent'
import Card from '../../components/Card'
import ProgressStep from '../../components/ProgressStep'
import Button from '../../components/Button'
import Loading from '../../components/Loading'
import Tooltip from '../../components/Tooltip'
import TooltipContent from '../../components/TooltipContent'
import DangerAlert from '../../components/DangerAlert'

import ArrowLeftIcon from '../../icons/ArrowLeftIcon'
import { fetchBranch } from '../../api/branch'
import { fetchServiceById } from '../../api/services'
import { createBooking } from '../../api/booking'

function BookingConfirmation() {
    const { branchId, serviceId, queueType } = useParams()
    const [searchParams] = useSearchParams()
    const PAGE_TITLE = `Konfirmasi Antrian`

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
                <ProgressStep active="2" total="3" />
            </div>
        </Header>

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
                    }}>Gagal membuat antrian</h4>

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
                            Antrian Anda sudah tersimpan dan sudah dikirimkan ke Email Anda sebagai bukti antrian telah dibooking
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
                            Lihat Status Antrian
                        </Link>
                    </TooltipContent>
                </Tooltip>}

                <Card style={{
                    marginBottom: '1.625rem'
                }}>
                    <h4 style={{
                        marginBottom: '1.625rem'
                    }}>Detail Booking</h4>

                    {branchRes.status === 'success' && serviceRes.status === 'success' && <div>
                        <div style={{
                            display: 'flex',
                            fontSize: '.875rem',
                            marginBottom: '1.125rem'
                        }}>
                            <div style={{
                                flex: '1',
                                color: '#A5A5A5'
                            }}>Tanggal Booking</div>

                            <div style={{
                                flex: '1',
                                color: '#103C7C',
                                fontWeight: '600'
                            }}>
                                {format(formatBrowser(searchParams.get('date')))}
                            </div>
                        </div>

                        <div style={{
                            display: 'flex',
                            fontSize: '.875rem',
                            marginBottom: '1.125rem'
                        }}>
                            <div style={{
                                flex: '1',
                                color: '#A5A5A5'
                            }}>Nama Cabang</div>

                            <div style={{
                                flex: '1',
                                color: '#103C7C',
                                fontWeight: '600'
                            }}>
                                {branch.name}
                            </div>
                        </div>

                        <div style={{
                            display: 'flex',
                            fontSize: '.875rem',
                            marginBottom: '1.125rem'
                        }}>
                            <div style={{
                                flex: '1',
                                color: '#A5A5A5'
                            }}>Layanan</div>

                            <div style={{
                                flex: '1',
                                color: '#103C7C',
                                fontWeight: '600'
                            }}>
                                {service.name}
                            </div>
                        </div>

                        <div style={{
                            display: 'flex',
                            fontSize: '.875rem',
                            marginBottom: '1.125rem'
                        }}>
                            <div style={{
                                flex: '1',
                                color: '#A5A5A5'
                            }}>Waktu</div>

                            <div style={{
                                flex: '1',
                                color: '#103C7C',
                                fontWeight: '600'
                            }}>
                                {slot.start_time} - {slot.end_time}
                            </div>
                        </div>
                    </div>}
                </Card>

                <Card>
                    <h4 style={{
                        marginBottom: '1.625rem'
                    }}>Detail User</h4>

                    {serviceRes.status === 'success' && <div>
                        <div style={{
                            display: 'flex',
                            fontSize: '.875rem',
                            marginBottom: '1.125rem'
                        }}>
                            <div style={{
                                flex: '1',
                                color: '#A5A5A5'
                            }}>Nama Lengkap</div>

                            <div style={{
                                flex: '1',
                                color: '#103C7C',
                                fontWeight: '600'
                            }}>
                                {searchParams.get('name')}
                            </div>
                        </div>

                        <div style={{
                            display: 'flex',
                            fontSize: '.875rem',
                            marginBottom: '1.125rem'
                        }}>
                            <div style={{
                                flex: '1',
                                color: '#A5A5A5'
                            }}>Email</div>

                            <div style={{
                                flex: '1',
                                color: '#103C7C',
                                fontWeight: '600'
                            }}>
                                {searchParams.get('email')}
                            </div>
                        </div>

                        <div style={{
                            display: 'flex',
                            fontSize: '.875rem',
                            marginBottom: '1.125rem'
                        }}>
                            <div style={{
                                flex: '1',
                                color: '#A5A5A5'
                            }}>No. Telepon</div>

                            <div style={{
                                flex: '1',
                                color: '#103C7C',
                                fontWeight: '600'
                            }}>
                                {searchParams.get('phone')}
                            </div>
                        </div>

                        <div style={{
                            display: 'flex',
                            fontSize: '.875rem',
                            marginBottom: '1.125rem'
                        }}>
                            <div style={{
                                flex: '1',
                                color: '#A5A5A5'
                            }}>Catatan</div>

                            <div style={{
                                flex: '1',
                                color: '#103C7C',
                                fontWeight: '600'
                            }}>
                                {searchParams.get('notes')}
                            </div>
                        </div>
                    </div>}
                </Card>
            </div>

            <div style={{
                boxShadow: '0px -4px 40px rgba(0, 0, 0, 0.13)',
                borderRadius: '16px 16px 0 0',
                padding: '1.75rem 1.375rem',
                margin: '1.625rem -1.375rem 0 -1.375rem'
            }}>
                <Button color="primary" onClick={handleClick} style={{
                    width: '100%',
                    fontSize: '1rem'
                }}>Booking Sekarang</Button>
            </div>
        </MainContent>
    </>
}

export default BookingConfirmation