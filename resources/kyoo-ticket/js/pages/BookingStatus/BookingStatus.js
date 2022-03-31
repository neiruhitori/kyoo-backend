import { useParams, Link } from 'react-router-dom'
import { useQuery } from 'react-query'
import { getBooking } from '../../api/booking'
import { fetchServiceById } from '../../api/services'
import { getAbrvDate, getDayName } from '../../utils/date'

import Header from '../../components/Header'
import Banner from '../../components/Banner'
import Chip from '../../components/Chip'
import H2 from '../../components/H2'
import Card from '../../components/Card'
import BlueCard from '../../components/BlueCard'
import InfoAlert from '../../components/InfoAlert'

import ClockIcon from '../../icons/ClockIcon'
import ArrowLeftIcon from '../../icons/ArrowLeftIcon'

export default function BookingStatus() {
    const { bookingId, queueType, branchId } = useParams()
    const PAGE_TITLE = `Status ${queueType}`

    let booking = null
    let service = null
    let slot = null
    
    const bookingQuery = useQuery(['booking', bookingId], () => getBooking(queueType, bookingId))
    const serviceQuery = useQuery('service', () => fetchServiceById(booking?.service_id, {
        queueType,
        date: booking?.date
    }), {
        enabled: bookingQuery.status === 'success'
    })

    if (bookingQuery.status === 'success') {
        booking = bookingQuery.data?.data
    }
    if (serviceQuery.status === 'success') {
        service = serviceQuery.data

        if (service.slot) {
            slot = service.slot.filter(s => {
                return s.day == getDayName(new Date(booking?.date), 'en')
            }).sort((a, b) => {
                if (a.start_time > b.start_time) {
                    return 1
                }
                
                if (a.start_time < b.start_time) {
                    return -1
                }

                return 0
            }).find((el, idx) => {
                el.session = idx + 1
                return el.start_time === booking.start_time
            })
        }
    }

    return <>
        <Banner imageUrl="/img/queue.jpeg" style={{
            position: 'absolute'
        }} />

        <Header bgType="blur">
            <div style={{
                marginRight:' 0.75rem'
            }}>
                <Link to={`/kyooTicket/${queueType}/${branchId}/services`} style={{
                    display: 'flex',
                    justifyContent: 'center',
                    alignItems: 'center'
                }}>
                    <ArrowLeftIcon color="#FFFFFF" />
                </Link>
            </div>

            <div style={{ textTransform: 'capitalize', textAlign: 'center', flex: '1 1 0%' }}>{PAGE_TITLE}</div>
        </Header>

        {bookingQuery.status === 'success' && <div style={{
            padding: '1.625rem 1.375rem',
            zIndex: '10'
        }}>
            <div style={{
                marginBottom: '1rem'
            }}>
                <Chip label={booking.industry_category} />
            </div>

            <div style={{
                width: '224px'
            }}>
                <H2 style={{
                    color: '#FFFFFF'
                }}>{booking.branch_name}</H2>
            </div>

            <Card style={{
                marginTop: '1.625rem',
                display: 'flex',
            }}>
                <div style={{
                    flex: '1 1 0%',
                    paddingRight: '1.125rem',
                    display: 'flex',
                    flexDirection: 'column'
                }}>
                    <div style={{
                        flexGrow: '1'
                    }}>
                        <h4 style={{
                            marginBottom: '0.5rem'
                        }}>{booking.service_name}</h4>

                        <div style={{
                            fontSize: '0.75rem',
                            color: '#A5A5A5',
                            marginTop: '0.75rem',
                            display: 'flex',
                            alignItems: 'center'
                        }}>
                            <ClockIcon
                                color="#A5A5A5"
                                width="0.75rem"
                                height="0.75rem"
                                style={{
                                    marginRight: '0.5rem'
                                }}
                            />
                            <span>
                                {getAbrvDate(new Date(booking.date))}
                            </span>
                            {serviceQuery.status === 'success' && !!slot && <>
                                <span style={{
                                    margin: '0 0.5rem'
                                }}>|</span>
                                <span style={{
                                    marginRight: '0.5rem'
                                }}>Sesi {slot.session}:</span>
                                <span style={{
                                    fontWeight: '700',
                                    color: '#007EC6'
                                }}>{booking.start_time} - {booking.end_time}</span>
                            </>}
                        </div>
                    </div>

                    {!!slot && <div style={{
                        flexGrow: '1'
                    }}>
                        <div style={{
                            fontSize: '.875rem',
                            display: 'inline-block',
                            marginBottom: '.375rem'
                        }}>
                            Kode Booking
                        </div>

                        <div style={{
                            color: '#007EC6',
                            fontWeight: '700',
                            fontSize: '1rem',
                            textTransform: 'uppercase'
                        }}>
                            {booking.booking_code}
                        </div>
                    </div>}
                </div>

                <BlueCard style={{
                    width: '119px',
                    margin: '-1.125rem -1.125rem -1.125rem 0'
                }}>
                    <div style={{
                        fontSize: '0.75rem',
                        marginBottom: '0.75rem',
                        fontWeight: '500'
                    }}>
                        Nomor Antrian
                    </div>

                    <div>
                        <span style={{
                            fontSize: '1.75rem',
                            fontWeight: '700',
                            color: '#FFFFFF'
                        }}>{booking.queue_no}</span>
                        {!!slot && <span style={{
                            color: '#D6D6D6',
                            fontSize: '1rem'
                        }}>/{booking.total_queue}</span>}
                    </div>

                    <div style={{
                        borderTop: '1px solid rgba(255, 255, 255, 0.16)',
                        margin: '.75rem 0'
                    }}></div>

                    <div style={{
                        fontSize: '0.75rem',
                        marginBottom: '0.75rem',
                        fontWeight: '500'
                    }}>
                        Antrian Sekarang
                    </div>

                    <span>{booking.current_queue}</span>
                </BlueCard>
            </Card>

            {!!booking.name && <Card style={{
                marginTop: '1.625rem'
            }}>
                <h4 style={{
                    marginBottom: '1.625rem'
                }}>Detail User</h4>

                <div>
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
                            {booking.name}
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
                            {booking.email}
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
                            {booking.phone}
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
                            {booking.notes}
                        </div>
                    </div>
                </div>
            </Card>}

            <InfoAlert style={{
                marginTop: '1.625rem',
                marginBottom: '1.625rem'
            }}>
                <h4 style={{
                    fontSize: '1rem',
                    marginBottom: '.375rem',
                    textTransform: 'capitalize'
                }}>Informasi Antrian</h4>

                <p style={{
                    lineHeight: '1.5',
                }}>
                    {queueType === 'onsite'
                        ? 'Anda akan dipanggil oleh counter yang Anda pilih sesuai dengan nomor antrian Anda.'
                        : 'Antrian dapat dilihat di Email Anda. Cukup lihatkan Nomor Antrian sesuai dengan waktu yang kamu tentukan.'}
                </p>
            </InfoAlert>
        </div>}
    </>
}