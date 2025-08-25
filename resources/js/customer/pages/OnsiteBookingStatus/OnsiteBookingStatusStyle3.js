import { useState, useEffect, useRef } from 'react'
import { useMutation, useQuery, useQueryClient } from 'react-query'
import { useNavigate, useParams, Link } from 'react-router-dom'
import styled from 'styled-components'
import html2canvas from 'html2canvas'

import { getBooking } from '../../api/booking'
import { format } from 'date-fns'
import id from 'date-fns/locale/id'
import en from 'date-fns/locale/en-US'
import { fetchBranch } from '../../api/branch'
import { createFeedback, fetchSurvey } from '../../api/feedback'
import usePromotions from '../../hooks/usePromotions'
import { formatBrowser, getDayName, getMonthAbrvName } from '../../utils/date'
import { getCookie } from '../../lib/helper'
import { fetchServiceById } from '../../api/services'
import useLocalization from '../../hooks/useLocalization'

import MainContent from '../../components/MainContent'
import TicketCard from '../../components/TicketCard'
import InfoAlert from '../../components/InfoAlert'
import KyooLogo from '../../components/KyooLogo'
import ChipWarning from '../../components/ChipWarning'
import ChipSuccess from '../../components/ChipSuccess'
import ChipDanger from '../../components/ChipDanger'
import Card from '../../components/Card'
import Rating from '../../components/Rating'
import Dialog from '../../components/Dialog'
import TicketRip from '../../components/TicketRip'
import Story from '../../components/Story'

import OnsiteQueueTicket from '../../templates/OnsiteQueueTicket'

import ArrowLeftIcon from '../../icons/ArrowLeftIcon'
import LocationIcon from '../../icons/LocationIcon'
import SaveIcon from '../../icons/SaveIcon'
import RedoIcon from '../../icons/RedoIcon'
import AngleRightIcon from '../../icons/AngleRightIcon'
import UserIcon from '../../icons/UserIcon'
import TicketStyle3 from '../../components/TicketStyle3'
import SurveyRenderer from './../../components/SurveyRenderer';

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


function getStatus(status) {
    if (status == 'served') {
        return 'Serve'
    }
    if (status == 'no show') {
        return 'No Show'
    }
    if (status == 'end served') {
        return 'End Served'
    }
    if (status == 'requeue') {
        return 'Requeue'
    }

    return 'Waiting'
}

    function TicketFooter(props) {
        const {t, locale} = useLocalization();
        return <div style={{
            display: 'flex',
            padding: '1.5rem 2.3rem',
            textAlign: 'center',
            justifyContent:'space-between'
          }}>
            <div>
              <h4 style={{ marginBottom: '0.3rem' }}>{t('Booking Code')}</h4>
              <p style={{ color: 'blue', padding:'0.5rem .75rem' }}>{props?.bookingCode}</p>
            </div>
            <div>
              <h4 style={{ marginBottom: '0.3rem' }}>{t('Queue Status')}</h4>
              <div> 
              {['waiting', 'served', 'check in', 'book'].includes(props?.status) &&
                <ChipWarning label={t(getStatus(props.status))} style={{  borderRadius: '20px', }} />}
                {props?.status === 'end served' && <ChipSuccess label={t(getStatus(props.status))} style={{  borderRadius: '20px', }}/>}
                {['canceled', 'no show'].includes(props?.status) && <ChipDanger label={t(getStatus(props.status))}  style={{  borderRadius: '20px', }}/>}</div>
            </div>
          </div>          
    }

function OnsiteBookingStatus(props) {
    const {t, locale} = useLocalization();
    const localeTime = locale == 'id' ? id : en
    const PAGE_TITLE = t('Onsite Queue Status')
    const REFETCH_INTERVAL = 15000
    const CLIENT_ID = getCookie('client_id')

    const { branchId, bookingId } = useParams()
    const queryClient = useQueryClient()
    const ticketRef = useRef(null)
    const navigate = useNavigate()

    const [rating, setRating] = useState({})
    const [isDialogShown, setIsDialogShown] = useState(false)
    const [isFeedbackSubmitted, setIsFeedbackSubmitted] = useState(false)
    const [isConfirmationDialogShown, setIsConfirmationDialogShown] = useState(false)
    const [isShowPromotion, setIsShowPromotion] = useState(true)

    let booking = null
    let branch = null
    let schedule = null
    let slot = null
    let surveyData = null
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
    const bookingQuery = useQuery(['booking', bookingId], () => getBooking('onsite', bookingId), {
        enabled: promotionsQuery.isSuccess,
        refetchInterval: () => {
            return ['end served', 'no show'].includes(booking?.status)
                ? false
                : REFETCH_INTERVAL
        }
    })
    const branchQuery = useQuery(['branch', branchId], () => fetchBranch(branchId), {
        enabled: bookingQuery.status === 'success'
    })
    const feedbackMutation = useMutation('feedback', (data) => createFeedback('onsite', bookingId, data))

    const serviceQuery = useQuery('service', () => fetchServiceById(booking?.service_id, {
             queueType:'onsite',
            date: booking?.date
        }), {
            enabled: bookingQuery.status === 'success'
        })

    const surveyQuery = useQuery('survey', () => fetchSurvey(branchId))
        if(surveyQuery.status == 'success'){
            surveyData = surveyQuery.data?.data
        }

    if (bookingQuery.status === 'success') {
        booking = bookingQuery.data?.data
    }

    if (bookingQuery.status === 'success' && branchQuery.status === 'success') {
        branch = branchQuery.data

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
                    return el.start_time === booking?.appointment_onsite?.start_time?.slice(0,5)
                })
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
            is_liked: false,
            bookingId
        }, {
            onSuccess: ({ data }) => {
                setIsFeedbackSubmitted(true)
                booking.rating = data.rating
                queryClient.invalidateQueries(['booking', bookingId])
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
                }} onClick={() => handlePrintTicket(booking.queue_no)}>{t('Save')}</button>

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
                }} onClick={() => navigate(`/customer/${branchId}/onsite/services`)}>{t('Yes')}</button>

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
            <div>
                <div>
                <Card>
                <div style={{
                        display: 'flex',
                        justifyContent: 'space-between',
                        alignItems: 'flex-start',
                        gap: '1rem',
                        marginBottom: '0.8rem',
                    }}>
                    <h4 style={{
                        fontWeight: '700',
                        fontSize: '1.5rem',
                        color: '#103C7C',
                        display: '-webkit-box',
                        WebkitLineClamp: 2,
                        WebkitBoxOrient: 'vertical',
                        overflow: 'hidden',
                        textOverflow: 'ellipsis',
                        maxWidth: '70%',
                    }}>
                        {booking?.service_name}
                    </h4>
                    <p style={{
                        backgroundColor: '#92EC47',
                        color: '#fff',
                        borderRadius: '20px',
                        textAlign: 'end',
                        fontSize: '1rem',
                        marginBottom: '.5rem',
                        padding: '0.5rem 1.2rem',
                        whiteSpace: 'nowrap',
                    }}>
                        {slot?.session ?  t('Session')+ ' ' + slot?.session : '-'}
                    </p>
                    </div>

                    <div style={{
                    display: 'flex',
                    justifyContent:'space-between',
                    }}>
                        <p style={{
                            fontSize: '1rem',
                            color: '#7A7A7A',
                            marginBottom: '.5rem',
                        }}>{booking?.date ? 
                            format(new Date(booking?.date), 
                            "EEEE, dd MMMM yyyy", {locale:localeTime})
                            : '-'}</p>
                        
                        <p style={{
                            fontSize: '1rem',
                            textAlign:'end',
                            marginBottom: '.5rem',
                        }}>{booking?.appointment_onsite?.start_time && booking?.appointment_onsite?.end_time ? 
                            `${booking?.appointment_onsite?.start_time.slice(0,5)} - ${booking?.appointment_onsite?.end_time.slice(0,5)}` 
                            : '-'}</p>
                    </div>
                </Card>
            </div>
                {branchQuery.status === 'success' && <TicketCard style={{
                    margin: '1.5rem 0px'
                }}>
                <TicketStyle3
                    queueNo={booking?.queue_no}
                    currentQueue={booking?.current_queue}
                />
                <TicketFooter
                    bookingCode={booking?.booking_code.toUpperCase()}
                    status={booking?.status}
                />
            </TicketCard>}
            </div>

            {!!booking?.name && <Card style={{
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
                                    <h4 style={{ fontSize:'large' }}>{t('Customer Details')}</h4>
                                </div>
            
                                
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
                                                    {booking?.name}
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
                                                        {booking?.phone}
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
                                                {booking?.appointment_onsite?.email || '-'}
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
                                                {booking?.appointment_onsite?.reason_for_visit || '-'}
                                                </h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </Card>}

            {branchType === 'premium' && booking.status === 'end served' && <SurveyRenderer
                            surveyData={surveyData}
                            booking={booking}
                            rating={rating}
                            setRating={setRating}
                            handleFeedbackClick={handleFeedbackClick}
                            isFeedbackSubmitted = {isFeedbackSubmitted}
                            t={t}
                        />}

            <InfoAlert>
                <h4 style={{
                    fontSize: '1rem',
                    marginBottom: '.375rem',
                    textTransform: 'capitalize'
                }}>{t('Onsite Queue Information')}</h4>

                <p style={{
                    lineHeight: '1.5',
                }}>
                   {t('You will be called by the counter you selected according to your queue number.')}
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

export default OnsiteBookingStatus