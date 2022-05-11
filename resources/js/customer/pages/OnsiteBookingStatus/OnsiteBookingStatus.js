import { useState, useMemo } from 'react'
import { useMutation, useQuery } from 'react-query'
import { Link, useParams } from 'react-router-dom'
import styled from 'styled-components'

import { getBooking } from '../../api/booking'
import { fetchBranch } from '../../api/branch'
import { createFeedback } from '../../api/feedback'
import { formatBrowser, getDayName, getMonthAbrvName } from '../../utils/date'

import MainContent from '../../components/MainContent'
import TicketCard from '../../components/Ticket'
import InfoAlert from '../../components/InfoAlert'
import KyooLogo from '../../components/KyooLogo'
import ChipWarning from '../../components/ChipWarning'
import ChipSuccess from '../../components/ChipSuccess'
import ChipDanger from '../../components/ChipDanger'
import Card from '../../components/Card'
import Rating from '../../components/Rating'

import ArrowLeftIcon from '../../icons/ArrowLeftIcon'
import LocationIcon from '../../icons/LocationIcon'

const BranchLogo = styled.img`
    display: inline-block;
    height: 4.5rem;
`

const BookingTimeCard = styled.div`
    background: rgba(255, 255, 255, 0.04);
    box-shadow: 0px 7px 40px rgba(0, 0, 0, 0.1);
    border-radius: 12px;
    width: 86px;
    height: 86px;
    padding: .875rem;
    color: #007EC6;
`

function TicketBody(props) {
    let status = ''

    if (props.status == 'waiting') {
        status = 'Menunggu'
    } else if (props.status == 'served') {
        status = 'Dilayani'
    } else if (props.status == 'no show') {
        status = 'Tidak Hadir'
    } else if (props.status == 'end served') {
        status = 'Selesai'
    } else if (props.status == 'requeue') {
        status = 'Antri Ulang'
    }

    let onsiteStatus = <ChipWarning label={status} />

    if (props.status == 'no show') {
        onsiteStatus = <ChipDanger label={status} />
    } else if (props.status == 'end served') {
        onsiteStatus = <ChipSuccess label={status} />
    }

    return <div style={{
        display: 'flex',
        alignItems: 'center',
        flexDirection: 'column'
    }}>
        <p style={{
            fontSize: '1.125rem',
            color: '#7A7A7A',
            marginBottom: '.625rem',
            textAlign: 'center'
        }}>Nomor Antrian</p>

        <h2 style={{
            fontWeight: '700',
            fontSize: '3.125rem',
            color: '#103C7C',
            textAlign: 'center',
            marginBottom: '.625rem'
        }}>
            {props.queueNo || 0}
        </h2>

        {onsiteStatus}
    </div>
}

function TicketFooter(props) {
    return <div style={{
        display: 'flex',
    }}>
        <BookingTimeCard style={{
            marginRight: '1.625rem',
        }}>
            <h3 style={{
                fontSize: '1.625rem',
                fontWeight: '700'
            }}>{formatBrowser(props.bookingDate).getDate()}</h3>
            <p style={{
                fontSize: '.875rem',
                fontWeight: '600'
            }}>{getMonthAbrvName(formatBrowser(props.bookingDate))}</p>

            <div style={{
                fontSize: '.625rem',
                marginTop: '.5rem'
            }}>
                {props.schedule?.start_time.slice(0, 5)} - {props.schedule?.end_time.slice(0, 5)}
            </div>
        </BookingTimeCard>

        <div style={{
            flex: '1 1 0%'
        }}>
            <h4>{props.serviceName}</h4>

            <div style={{
                display: 'flex',
                marginTop: '1rem'
            }}>
                <LocationIcon color="#007EC6" height="1.25rem" width="1.25rem" />

                <div style={{
                    fontSize: '.875rem',
                    color: '#7A7A7A',
                    paddingLeft: '.625rem',
                    lineHeight: '1.5'
                }}>
                    {props.branch?.name}
                    <p>{props.branch?.address}</p>
                </div>
            </div>
        </div>
    </div>
}

function OnsiteBookingStatus() {
    const PAGE_TITLE = 'Status Antrian Onsite'
    const { branchId, bookingId } = useParams()

    const [rating, setRating] = useState(0)
    const [allowBooking, setAllowBooking] = useState(true)

    let booking = null
    let branch = null
    let schedule = null
    let branchType = 'free'

    const bookingQuery = useQuery(['booking', bookingId], () => getBooking('onsite', bookingId))
    const branchQuery = useQuery(['branch', branchId], () => fetchBranch(branchId), {
        enabled: bookingQuery.status === 'success'
    })
    const feedbackMutation = useMutation('feedback', (data) => createFeedback('onsite', bookingId, data))

    if (bookingQuery.status === 'success') {
        booking = bookingQuery.data?.data
    }

    useMemo(() => {
        if (booking) {
            setRating(booking.rating)
            setAllowBooking(!booking.rating)
        }
    }, [booking])

    if (bookingQuery.status === 'success' && branchQuery.status === 'success') {
        branch = branchQuery.data

        if (branch.branch_type.is_premium) {
            branchType = 'premium'
        }

        schedule = branch.schedule.find(v => {
            return v.day === getDayName(formatBrowser(booking.date), 'en')
        })
    }

    function handleFeedbackClick() {
        feedbackMutation.mutate({
            rating,
            is_liked: false
        }, {
            onSuccess: (data) => {
                if (data.success) {
                    setAllowBooking(false)
                }
            }
        })
    }

    return <>
        <div style={{
            height: '3.2rem',
            padding: '0 1.375rem',
            display: 'flex',
            alignItems: 'center'
        }}>
            <Link to={`/customer/${branchId}/${queueType}/services`} style={{
                display: 'flex',
                justifyContent: 'center',
                alignItems: 'center'
            }}>
                <ArrowLeftIcon />
            </Link>

            <div style={{ textTransform: 'capitalize', textAlign: 'center', flex: '1 1 0%' }}>{PAGE_TITLE}</div>
        </div>

        {bookingQuery.status === 'success' && <MainContent>
            <div>
                {!!branch?.logo && <div style={{
                    textAlign: 'center',
                    marginBottom: '1.5rem'
                }}>
                    <BranchLogo src={`/storage/${branch.logo}`}/>
                </div>}

                {bookingQuery.status === 'success' && branchQuery.status === 'success' && <TicketCard
                    body={<TicketBody
                        queueNo={booking.queue_no}
                        status={booking.status}
                    />}
                    footer={<TicketFooter
                        serviceName={booking.service_name}
                        bookingDate={booking.date}
                        branch={branch}
                        schedule={schedule}
                    />}
                    style={{
                        marginBottom: '2rem'
                    }}
                />}
            </div>

            {branchType === 'premium' && booking.status === 'end served' && <Card style={{
                marginBottom: '2rem',
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
                        onRateClick={rate => allowBooking && setRating(rate)}
                    />
                </div>
            
                {allowBooking && <div style={{
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

            <InfoAlert>
                <h4 style={{
                    fontSize: '1rem',
                    marginBottom: '.375rem',
                    textTransform: 'capitalize'
                }}>Informasi Antrian Onsite</h4>

                <p style={{
                    lineHeight: '1.5',
                }}>
                    Anda akan dipanggil oleh counter yang Anda pilih sesuai dengan nomor antrian Anda
                </p>
            </InfoAlert>

            <div style={{
                display: 'flex',
                fontSize: '.875rem',
                color: '#7A7A7A',
                justifyContent: 'center',
                alignItems: 'center',
                padding: '1.125rem 0',
                marginTop: 'auto'
            }}>
                Powered by
                <KyooLogo style={{ marginLeft: '0.5rem' }} />
            </div>
        </MainContent>}
    </>
}

export default OnsiteBookingStatus