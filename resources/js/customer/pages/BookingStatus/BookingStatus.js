import { useState, useMemo } from 'react'
import { useParams, Link } from 'react-router-dom'
import { useQuery, useMutation } from 'react-query'
import { getBooking, cancelBooking } from '../../api/booking'
import { fetchServiceById } from '../../api/services'
import { createFeedback } from '../../api/feedback'
import { fetchBranch } from '../../api/branch'
import { getTermConditionByBranchId } from '../../api/termCondition'
import { getAbrvDate, getDayName, formatBrowser } from '../../utils/date'

import Header from '../../components/Header'
import Banner from '../../components/Banner'
import Chip from '../../components/Chip'
import H2 from '../../components/H2'
import Card from '../../components/Card'
import BlueCard from '../../components/BlueCard'
import InfoAlert from '../../components/InfoAlert'
import ChipSuccess from '../../components/ChipSuccess'
import ChipWarning from '../../components/ChipWarning'
import ChipDanger from '../../components/ChipDanger'
import Rating from '../../components/Rating'
import Button from '../../components/Button'
import Dialog from '../../components/Dialog'

import ClockIcon from '../../icons/ClockIcon'
import ArrowLeftIcon from '../../icons/ArrowLeftIcon'
import { cancelAppointment } from '../../api/appointment'

export default function BookingStatus() {
    const { bookingId, queueType, branchId } = useParams()
    const PAGE_TITLE = `Status ${queueType}`

    const [rating, setRating] = useState(0)
    const [allowRate, setAllowRate] = useState(false)
    const [isShowDialog, setIsShowDialog] = useState(false)
    
    const bookingQuery = useQuery('booking', () => getBooking(queueType, bookingId))
    const serviceQuery = useQuery('service', () => fetchServiceById(booking?.service_id, {
        queueType,
        date: booking?.date
    }), {
        enabled: bookingQuery.status === 'success'
    })
    const branchQuery = useQuery('branch', () => fetchBranch(booking?.branch_id), {
        enabled: bookingQuery.status === 'success'
    })
    const feedbackMutation = useMutation('feedback', (data) => createFeedback(queueType, bookingId, data))
    const termConditionQuery = useQuery('termCondition', () => getTermConditionByBranchId(branchId))

    const booking = bookingQuery.data?.data
    const service = serviceQuery.data?.data
    const branch = branchQuery.data?.data
    const termCondition = termConditionQuery.data

    const branchType = branch?.branch_type.is_premium
    const isAppointmentUnfinished = ['book', 'check in', 'waiting'].includes(booking?.status)
    const slot = service?.slot
        .filter(s => {
            return s.day == getDayName(formatBrowser(booking?.date), 'en')
        })
        .sort((a, b) => {
            if (a.start_time > b.start_time) return 1
            if (a.start_time < b.start_time) return -1

            return 0
        })
        .find((el, idx) => {
            el.session = idx + 1
            return el.start_time === booking.start_time
        })

    const bookingStatusMap = {
        'book': 'Dipesan',
        'check in': 'Check In',
        'served': 'Dilayani',
        'end served': 'Selesai',
        'no show': 'Tidak Hadir',
        'canceled': 'Dibatalkan'
    }
    const bookingStatus = bookingStatusMap[booking?.status]

    useMemo(() => {
        if (booking) {
            setRating(booking.rating)
            setAllowRate(!booking.rating)
        }
    }, [booking])

    function handleFeedbackClick() {
        feedbackMutation.mutate({
            rating,
            is_liked: false
        }, {
            onSuccess: (data) => {
                if (data.success) {
                    setAllowRate(false)
                }
            }
        })
    }

    function handleCancelClick() {
        setIsShowDialog(true)
    }

    function handleConfirmCancel(id) {
        cancelAppointment(id)
        bookingQuery.refetch()
        setIsShowDialog(false)
    }

    function createTermConditionMarkup() {  
        return {__html: termCondition?.body}
    }

    return <>
        <Banner imageUrl={branch?.photo} style={{
            position: 'absolute'
        }} />

        <Header bgType="blur">
            <div style={{
                height: '3.2rem',
                display: 'flex'
            }}>
                <Link to={`/customer/${branchId}/${queueType}/services`} style={{
                    display: 'flex',
                    justifyContent: 'center',
                    alignItems: 'center',
                    padding: '.85rem 1.375rem'
                }}>
                    <ArrowLeftIcon color="#FFFFFF" />
                </Link>
            </div>

            <div style={{
                textTransform: 'capitalize',
                textAlign: 'center',
                flex: '1 1 0%',
                margin: '0 auto'
            }}>
                {PAGE_TITLE}
            </div>
        </Header>

        <div style={{
            padding: '1.625rem 1.375rem',
            zIndex: '10'
        }}>
            <div style={{
                marginBottom: '1rem'
            }}>
                <Chip label={booking?.industry_category} />
            </div>

            <div>
                <H2 style={{
                    color: '#FFFFFF'
                }}>{booking?.branch_name}</H2>
            </div>

            <Card style={{
                marginTop: '1.625rem',
                display: 'flex',
            }}>
                <div style={{
                    flex: '1 1 0%',
                    paddingRight: '1.125rem',
                    display: 'flex',
                    flexDirection: 'column',
                    gap: '1rem'
                }}>
                    <div style={{
                        flexGrow: '1'
                    }}>
                        <h4 style={{
                            marginBottom: '0.5rem'
                        }}>{booking?.service_name}</h4>

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
                                {!!booking && getAbrvDate(formatBrowser(booking.date))}
                            </span>
                            <span style={{
                                margin: '0 0.5rem'
                            }}>|</span>
                            <span style={{
                                marginRight: '0.5rem'
                            }}>Sesi {slot?.session}:</span>
                            <span style={{
                                fontWeight: '700',
                                color: '#007EC6'
                            }}>{booking?.start_time} - {booking?.end_time}</span>
                        </div>
                    </div>

                    <div style={{
                        flexGrow: '1',
                        display: 'flex',
                        gap: '.75rem'
                    }}>
                        <div style={{
                            flex: '1 1 0%'
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
                                {booking?.booking_code}
                            </div>
                        </div>

                        <div style={{
                            flex: '1 1 0%'
                        }}>
                            <div style={{
                                fontSize: '.875rem',
                                display: 'inline-block',
                                marginBottom: '.625rem'
                            }}>
                                Status Antrian
                            </div>

                            <div>
                                {['waiting', 'served', 'check in', 'book'].includes(booking?.status) &&
                                <ChipWarning label={bookingStatus} />}
                                {booking?.status === 'end served' && <ChipSuccess label={bookingStatus} />}
                                {['canceled', 'no show'].includes(booking?.status) && <ChipDanger label={bookingStatus} />}
                            </div>
                        </div>
                    </div>
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
                        }}>{booking?.queue_no}</span>
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

                    <span>{booking?.current_queue || 0}</span>
                </BlueCard>
            </Card>

            {!!booking?.name && <Card style={{
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
                            {booking?.name}
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
                            {booking?.email}
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
                            {booking?.phone}
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
                            {booking?.notes}
                        </div>
                    </div>
                </div>
            </Card>}

            {branchType === 'premium' && queueType === 'appointment' && booking?.status === 'end served' && <Card style={{
                margin: '1.625rem 0',
                padding: '1.625rem'
            }}>
                <p style={{
                    textAlign: 'center'
                }}>Seberapa puas Anda terhadap layanan kami?</p>
            
                <div style={{
                    marginTop: '1.125rem'
                }}>
                    <Rating
                        rate={rating}
                        onRateClick={rate => allowRate && setRating(rate)}
                    />
                </div>
            
                {allowRate && <div style={{
                    textAlign: 'center',
                    marginTop: '1.125rem'
                }}>
                    <button type="submit" style={{
                        padding: '.625rem 1.125rem',
                        borderRadius: '14px',
                        color: '#FFFFFF',
                        backgroundColor: '#007EC6',
                        border: 'none'
                    }} onClick={handleFeedbackClick}>Kirim Feedback</button>
                </div>}
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

            {termCondition?.body && <InfoAlert>
                <h4 style={{
                    fontSize: '1rem',
                    marginBottom: '.375rem',
                    textTransform: 'capitalize'
                }}>Syarat & Ketentuan</h4>

                <p
                    style={{
                        lineHeight: '1.5',
                    }}
                    dangerouslySetInnerHTML={createTermConditionMarkup()}
                />
            </InfoAlert>}
        </div>

        {isAppointmentUnfinished && <div style={{
            padding: '0 1.625rem',
            marginTop: 'auto',
            marginBottom: '1.125rem'
        }}>
            <Button
                color="secondary"
                style={{ width: '100%' }}
                onClick={handleCancelClick}
            >
                Batalkan Appoinment
            </Button>
        </div>}

        {isShowDialog && <Dialog>
            <div style={{
                marginBottom: '1.5rem'
            }}>
                <p style={{
                    textAlign: 'center',
                    margin: '0 auto',
                    lineHeight: '1.5'
                }}>Apakah Anda yakin ingin membatalkan appointment?</p>
            </div>

            <div style={{
                display: 'flex',
                justifyContent: 'center',
                gap: '1rem'
            }}>
                <Button color="primary" onClick={() => handleConfirmCancel(booking?.id)}>Ya, Batalkan</Button>
                <Button color="secondary" onClick={() => setIsShowDialog(false)}>Tidak</Button>
            </div>
        </Dialog>}
    </>
}