import { useState, useEffect, useRef } from 'react'
import { useMutation, useQuery, useQueryClient } from 'react-query'
import { useNavigate, useParams, Link } from 'react-router-dom'
import QRCode from 'react-qr-code';
import styled from 'styled-components'
import html2canvas from 'html2canvas'

import { format } from 'date-fns'
import { getAppointmentOnsiteById } from '../../api/appointmentOnsite'
import { fetchBranch } from '../../api/branch'
import { createFeedback } from '../../api/feedback'
import usePromotions from '../../hooks/usePromotions'
import { formatBrowser, getDayName, getMonthAbrvName } from '../../utils/date'
import { getCookie } from '../../lib/helper'
import { fetchServiceById } from '../../api/services'

import MainContent from '../../components/MainContent'
import InfoAlert from '../../components/InfoAlert'
import KyooLogo from '../../components/KyooLogo'
import ChipWarning from '../../components/ChipWarning'
import ChipSuccess from '../../components/ChipSuccess'
import ChipDanger from '../../components/ChipDanger'
import Card from '../../components/Card'
import Rating from '../../components/Rating'
import Dialog from '../../components/Dialog'
import Story from '../../components/Story'
import TicketCard from '../../components/TicketCard'
import TicketRip from '../../components/TicketRip'

import OnsiteQueueTicket from '../../templates/OnsiteQueueTicket'

import ArrowLeftIcon from '../../icons/ArrowLeftIcon'
import SaveIcon from '../../icons/SaveIcon'
import RedoIcon from '../../icons/RedoIcon'
import useLocalization from '../../hooks/useLocalization'

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

const OnsiteChipSuccess = styled(ChipSuccess)`
    font-size: .875rem;
`

const OnsiteChipWarning = styled(ChipWarning)`
    font-size: .875rem;
`

const OnsiteChipDanger = styled(ChipDanger)`
    font-size: .875rem;
`
const Wrapper = styled.div`
    display: grid;
    grid-template-columns: 10rem 10px 1fr; 
    align-items: start; 
    gap: 0.5rem;
    margin-bottom: 1rem;
`;

const Title = styled.h4`
    margin: 0;
    white-space: nowrap;
`;

const Content = styled.p`
    margin: 0;
    word-break: break-word;
`;

    function TicketHead(props) {
        const {t, locale} = useLocalization();
        return <div style={{
            padding: '1.75rem',
        }}>
                <div style={{
                textAlign:'center',
                marginBottom: '0.8rem',
                }}>
                    <h4 style={{
                        fontWeight: '700',
                        fontSize: 'medium',
                        marginBottom:'1rem'
                    }}>
                        {props?.branchName}
                    </h4>
                    <h4 style={{
                        fontWeight: '700',
                        fontSize: '4rem',
                        color: '#103C7C',
                        marginBottom:'1rem'
                    }}>
                        {props?.bookingCode.toUpperCase()}
                    </h4>
                </div>
        </div>
    }
    function TicketFooter(props) {
        return (
            <div style={{ padding: '1.75rem' }} {...props}>
                <Wrapper>
                    <Title>Nama Layanan</Title>
                    <span>:</span>
                    <Content>{props.booking?.service_name}</Content>
                </Wrapper>
    
                <Wrapper>
                    <Title>Tanggal</Title>
                    <span>:</span>
                    <Content>{format(formatBrowser(props.booking?.date),'dd MMMM yyyy')}</Content>
                </Wrapper>
    
                <Wrapper>
                    <Title>Slot Waktu</Title>
                    <span>:</span>
                    <Content>{props.booking?.start_time.slice(0,5) + ` - ` + props.booking?.end_time.slice(0,5)}</Content>
                </Wrapper>
            </div>
        );
    }

function AppointmentOnsiteBookingStatus(props) {
    const {t, locale} = useLocalization();
    const PAGE_TITLE = t('Queue Status')
    const REFETCH_INTERVAL = 15000
    const CLIENT_ID = getCookie('client_id')

    const { branchId,queueType, bookingId } = useParams()
    const queryClient = useQueryClient()
    const ticketRef = useRef(null)
    const navigate = useNavigate()

    const [rating, setRating] = useState(0)
    const [isDialogShown, setIsDialogShown] = useState(false)
    const [isConfirmationDialogShown, setIsConfirmationDialogShown] = useState(false)
    const [isShowPromotion, setIsShowPromotion] = useState(true)

    let booking = null
    let branch = null
    let schedule = null
    let slot = null
    let branchType = 'free'

    useEffect(() => {
        if (!sessionStorage.getItem('dialog-shown')) {
            setIsDialogShown(true)
            sessionStorage.setItem('dialog-shown', 'true')
        }

        window.Echo.channel(`onsite_queues.${CLIENT_ID}`)
            .listen('OnsiteQueueUpdated', () => {
                queryClient.invalidateQueries(['booking', bookingId])
            })

        return () => {
            sessionStorage.removeItem('dialog-shown')
            window.Echo.channel(`onsite_queues.${CLIENT_ID}`)
                .stopListening('OnsiteQueueUpdated')
        }
    }, [])

    const promotionsQuery = usePromotions(branchId)
    const bookingQuery = useQuery(['booking', bookingId], () => getAppointmentOnsiteById(bookingId), {
        enabled: promotionsQuery.isSuccess
    })
    const serviceQuery = useQuery('service', () => fetchServiceById(booking?.service_id, {
        queueType,
        date: booking?.date
    }), {
        enabled: bookingQuery.status === 'success'
    })
    const branchQuery = useQuery(['branch', branchId], () => fetchBranch(branchId), {
        enabled: bookingQuery.status === 'success'
    })
    const feedbackMutation = useMutation('feedback', (data) => createFeedback('onsite', bookingId, data))

    if (bookingQuery.status === 'success') {
        booking = bookingQuery.data?.data
        const service = serviceQuery.data
        const dayName = getDayName(formatBrowser(booking?.date), 'en');

        slot = service?.slot
        .filter(s => s.day === dayName)
        .sort((a, b) => {
            if (a.start_time > b.start_time) return 1
            if (a.start_time < b.start_time) return -1

            return 0
        }).find((el, idx) => {
            el.session = idx + 1
            return el.start_time === booking?.start_time?.slice(0,5)
        })


        if(booking?.is_used) {
            navigate(`/customer/${branchId}/onsite/booking-status/${booking.direct_queue_id}`)
        }
    }
    

    if (bookingQuery.status === 'success' && branchQuery.status === 'success') {
        branch = branchQuery.data

        schedule = branch.schedule.find(v => {
            return v.day === getDayName(formatBrowser(booking.date), 'en')
        })
    }

    if (branch?.branch_type.is_premium) {
        branchType = 'premium'
    }

    function handleFeedbackClick() {
        feedbackMutation.mutate({
            rating,
            is_liked: false
        }, {
            onSuccess: ({ data }) => {
                booking.rating = data.rating
                queryClient.invalidateQueries(['booking', bookingId])
            }
        })
    }

    function handlePrintTicket(bookingCode) {
        html2canvas(ticketRef.current).then((canvas) => {
            const imgUrl = canvas.toDataURL('image/png');

            const a = document.createElement('a')
            a.href = imgUrl
            a.download = `${bookingCode}.png`
            a.click()

            setIsDialogShown(false)
        })
    }

    function handleStoryDone() {
        setIsShowPromotion(false)
    }

    return <>
        {promotionsQuery.isSuccess &&
        !!promotionsQuery.data.length &&
        isShowPromotion &&
        branch?.branch_configuration.promotion &&
        <Story
            stories={promotionsQuery.data}
            onDone={handleStoryDone}
            style={{
                position: 'absolute',
                top: '0',
                left: '0',
                right: '0',
                bottom: '0',
                zIndex: '99999'
            }}
        />}

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

        {!!booking && isDialogShown && <Dialog>
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
                }}>{t('Save your queue?')}</h4>
                <p style={{
                    textAlign: 'center',
                    margin: '0 auto',
                    lineHeight: '1.5'
                }}>{t('Save your queue ticket to avoid losing your queue information')}</p>
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
                }} onClick={() => handlePrintTicket(booking.booking_code)}>{t('Save')}</button>

                <button style={{
                    width: '142px',
                    padding: '.875rem',
                    borderRadius: '8px',
                    border: 'none',
                    outline: 'none',
                    fontWeight: 'bold',
                    backgroundColor: '#D8D8D8',
                    fontSize: '1rem'
                }} onClick={() => setIsDialogShown(false)}>{t('No')}</button>
            </div>
        </Dialog>}

        {isConfirmationDialogShown && <Dialog>
            <div style={{
                marginBottom: '1.5rem'
            }}>
                <p style={{
                    textAlign: 'center',
                    margin: '0 auto',
                    lineHeight: '1.5'
                }}>{t('Are you sure you want to leave this page?')}</p>
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
                    border: 'none',
                    outline: 'none',
                    color: '#FFFFFF',
                    backgroundColor: '#007EC6',
                    fontSize: '1rem'
                }} onClick={() => navigate(`/customer/${branchId}/onsite/services/two-layer`)}>Iya</button>

                <button style={{
                    width: '142px',
                    padding: '.875rem',
                    borderRadius: '8px',
                    border: 'none',
                    outline: 'none',
                    backgroundColor: '#D8D8D8',
                    fontSize: '1rem'
                }} onClick={() => setIsConfirmationDialogShown(false)}>{t('No')}</button>
            </div>
        </Dialog>}

        <div style={{
            display: 'flex',
            alignItems: 'center'
        }}>
            <button
                type="button"
                style={{
                    display: 'flex',
                    justifyContent: 'center',
                    alignItems: 'center',
                    height: '3.2rem',
                    outline: 'none',
                    backgroundColor: 'transparent',
                    border: 'none',
                    padding: '.85rem 1.375rem'
                }}
                onClick={() => setIsConfirmationDialogShown(true)}
            >
                <ArrowLeftIcon />
            </button>

            <div style={{
                textTransform: 'capitalize',
                textAlign: 'center',
                flex: '1 1 0%'
            }}>{PAGE_TITLE}</div>

            <button
                type="button"
                style={{
                    display: 'flex',
                    justifyContent: 'center',
                    alignItems: 'center',
                    height: '3.2rem',
                    outline: 'none',
                    backgroundColor: 'transparent',
                    border: 'none',
                    padding: '.85rem 1.375rem'
                }}
                onClick={() => location.reload()}
            >
                <RedoIcon />
            </button>
        </div>

        {bookingQuery.status === 'success' && <MainContent>
            
            <TicketCard style={{ margin:'5rem 2rem' }}>
                    <TicketHead
                        branchName={branch?.name}
                        bookingCode={booking?.booking_code}
                    />
                    <TicketRip/>
                    <TicketFooter 
                        booking={booking}
                    />
            </TicketCard>


            {branchType === 'premium' && booking.status === 'end served' && <Card style={{
                marginBottom: '1.5rem',
                padding: '1.625rem'
            }}>
                <p style={{
                    textAlign: 'center'
                }}>{t('How satisfied are you with our service?')}</p>

                <div style={{
                    marginTop: '1.125rem'
                }}>
                    <Rating
                        rate={booking.rating || rating}
                        onRateClick={rate => {
                            if (!booking.rating) setRating(rate)
                        }}
                    />
                </div>

                {!booking.rating && <div style={{
                    textAlign: 'center',
                    marginTop: '1.125rem'
                }}>
                    <button type="submit" style={{
                        padding: '.625rem 1.125rem',
                        borderRadius: '14px',
                        color: '#FFFFFF',
                        backgroundColor: '#007EC6',
                        border: 'none'
                    }} onClick={handleFeedbackClick}>{t('Send Feedback')}</button>
                </div>}
            </Card>}

            <InfoAlert>
                <h4 style={{
                    fontSize: '1rem',
                    marginBottom: '.375rem',
                    textTransform: 'capitalize'
                }}>{t('Onsite Queue Information')}</h4>

                <p style={{
                    lineHeight: '1.5',
                }}>
                    {t('You must save the booking code to get the queue number according to your booking date')}
                </p>
            </InfoAlert>
        </MainContent>}

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
    </>
}

export default AppointmentOnsiteBookingStatus
