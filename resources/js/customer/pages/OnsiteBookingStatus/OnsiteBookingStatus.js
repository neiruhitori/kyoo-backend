import { useState, useMemo, useEffect, useRef } from 'react'
import { useMutation, useQuery } from 'react-query'
import { Link, useParams } from 'react-router-dom'
import styled from 'styled-components'
import html2canvas from 'html2canvas'

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

import OnsiteQueueTicket from '../../templates/OnsiteQueueTicket'

import ArrowLeftIcon from '../../icons/ArrowLeftIcon'
import LocationIcon from '../../icons/LocationIcon'
import SaveIcon from '../../icons/SaveIcon'

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

function getStatus(status) {
    if (status == 'served') {
        return 'Dilayani'
    }
    if (status == 'no show') {
        return 'Tidak Hadir'
    }
    if (status == 'end served') {
        return 'Selesai'
    }
    if (status == 'requeue') {
        return 'Antri Ulang'
    }

    return 'Menunggu'
}

function TicketBody(props) {
    return <div style={{
        display: 'flex',
        alignItems: 'center',
        flexDirection: 'column'
    }}>
        <p style={{
            fontSize: '1rem',
            color: '#7A7A7A',
            marginBottom: '.5rem',
            textAlign: 'center'
        }}>Nomor Antrian</p>

        <h2 style={{
            fontWeight: '700',
            fontSize: '3.625rem',
            color: '#103C7C',
            textAlign: 'center',
            marginBottom: '.625rem'
        }}>
            {props.queueNo || 0}
        </h2>
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

function OnsiteBookingStatus(props) {
    const PAGE_TITLE = 'Status Antrian Onsite'
    const { branchId, bookingId } = useParams()

    const [rating, setRating] = useState(0)
    const [allowBooking, setAllowBooking] = useState(true)
    const [isDialogShown, setIsDialogShown] = useState(false)

    let booking = null
    let branch = null
    let schedule = null
    let branchType = 'free'
    const ticketRef = useRef(null)

    useEffect(() => {
        setIsDialogShown(true)

        return () => setIsDialogShown(false)
    }, [])

    const bookingQuery = useQuery(['booking', bookingId], () => getBooking('onsite', bookingId))
    const branchQuery = useQuery(['branch', branchId], () => fetchBranch(branchId), {
        enabled: bookingQuery.status === 'success'
    })
    const feedbackMutation = useMutation('feedback', (data) => createFeedback('onsite', bookingId, data))

    let onsiteStatus = <ChipWarning label={getStatus('waiting')} />

    if (bookingQuery.status === 'success') {
        booking = bookingQuery.data?.data

        if (booking.status == 'no show') {
            onsiteStatus = <ChipDanger label={getStatus(booking.status)} />
        } else if (booking.status == 'end served') {
            onsiteStatus = <ChipSuccess label={getStatus(booking.status)} />
        } else {
            onsiteStatus = <ChipWarning label={getStatus(booking.status)} />
        }
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

    function handlePrintTicket(queueNo) {
        html2canvas(ticketRef.current).then((canvas) => {
            const imgUrl = canvas.toDataURL('image/png');

            const a = document.createElement('a')
            a.href = imgUrl
            a.download = `${queueNo}.png`
            a.click()

            setIsDialogShown(false)
        })
    }

    return <>
        {!!booking && !!branch && <OnsiteQueueTicket
            ref={ticketRef}
            booking={booking}
            branch={branch}
            style={{
                position: 'absolute',
                bottom: '100%',
                left: 0,
                right: 0
            }}
        />}

        {!!booking && isDialogShown && <div style={{
            backgroundColor: 'rgba(0, 0, 0, 0.5)',
            position: 'fixed',
            top: '0',
            right: '0',
            bottom: '0',
            left: '0',
            display: 'flex',
            justifyContent: 'center',
            alignItems: 'center',
            zIndex: '9999'
        }}>
            <Card style={{
                padding: '2.5rem',
                maxWidth: '382px'
            }}>
                <div style={{
                    display: 'flex',
                    width: '68px',
                    height: '68px',
                    borderRadius: '999999px',
                    alignItems: 'center',
                    justifyContent: 'center',
                    backgroundColor: '#D0E9FB',
                    margin: '0 auto',
                    marginBottom: '1.5rem'
                }}>
                    <SaveIcon color="#0172CB" style={{ width: '30px', height: 'auto' }} />
                </div>

                <div style={{
                    marginBottom: '1.5rem'
                }}>
                    <h4 style={{
                        textAlign: 'center',
                        fontSize: '1.3rem',
                        marginBottom: '1rem'
                    }}>Simpan Antrianmu?</h4>
                    <p style={{
                        textAlign: 'center',
                        margin: '0 auto',
                        lineHeight: '1.5'
                    }}>Simpan tiket antrian agar tidak kehilangan informasi antrianmu</p>
                </div>

                <div style={{
                    display: 'flex',
                    justifyContent: 'center',
                    gap: '1rem'
                }}>
                    <button style={{
                        width: '142px',
                        padding: '.875rem',
                        borderRadius: '8px',
                        border: '1px solid #D8D8D8',
                        outline: 'none',
                        fontWeight: 'bold',
                        backgroundColor: '#FFFFFF',
                        fontSize: '1rem'
                    }} onClick={() => handlePrintTicket(booking.queue_no)}>Simpan</button>

                    <button style={{
                        width: '142px',
                        padding: '.875rem',
                        borderRadius: '8px',
                        border: 'none',
                        outline: 'none',
                        fontWeight: 'bold',
                        backgroundColor: '#D8D8D8',
                        fontSize: '1rem'
                    }} onClick={() => setIsDialogShown(false)}>Tidak</button>
                </div>
            </Card>
        </div>}

        <div style={{
            height: '3.2rem',
            padding: '0 1.375rem',
            display: 'flex',
            alignItems: 'center'
        }}>
            <Link to={`/customer/${branchId}/onsite/services`} style={{
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

                <div style={{
                    display: 'flex',
                    justifyContent: 'space-between',
                    alignItems: 'end',
                    marginBottom: '1rem'
                }}>
                    <div>
                        <h4 style={{
                            fontWeight: '700',
                            fontSize:  '1.5rem'
                        }}>{booking.total_remaining_queue}</h4>
                        <p style={{
                            color: '#7A7A7A',
                            fontSize: '.875rem',
                            marginTop: '.625rem'
                        }}>Antrian Tersisa</p>
                    </div>

                    <div style={{
                        textAlign: 'right'
                    }}>
                        {onsiteStatus}
                        <p style={{
                            color: '#7A7A7A',
                            fontSize: '.875rem',
                            marginTop: '.625rem'
                        }}>Status Antrian</p>
                    </div>
                </div>

                {branchQuery.status === 'success' && <TicketCard
                    body={<TicketBody
                        queueNo={booking.queue_no}
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