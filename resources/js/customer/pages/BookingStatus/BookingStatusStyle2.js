import { useState, useMemo } from 'react'
import { useParams, Link } from 'react-router-dom'
import { useQuery, useMutation } from 'react-query'
import { getBooking, cancelBooking } from '../../api/booking'
import { fetchServiceById } from '../../api/services'
import { createFeedback } from '../../api/feedback'
import { fetchBranch } from '../../api/branch'
import { getTermConditionByBranchId } from '../../api/termCondition'
import { cancelAppointment } from '../../api/appointment'
import usePromotions from '../../hooks/usePromotions'
import { getAbrvDate, getDayName, formatBrowser, getMonthAbrvName } from '../../utils/date'
import LocationIcon from '../../icons/LocationIcon'
import { format, eachMonthOfInterval, parseISO, addDays } from 'date-fns'
import id from 'date-fns/locale/id'
import Header from '../../components/Header'
import Banner from '../../components/Banner'
import Chip from '../../components/Chip'
import H2 from '../../components/H2'
import Card from '../../components/Card'
import BlueCard from '../../components/BlueCard'
import InfoAlert from '../../components/InfoAlert'
import ChipSuccess from '../../components/ChipSuccess2'
import ChipWarning from '../../components/ChipWarning2'
import ChipDanger from '../../components/ChipDanger2'
import Rating from '../../components/Rating'
import Button from '../../components/Button'
import Dialog from '../../components/Dialog'
import Story from '../../components/Story'

import ClockIcon from '../../icons/ClockIcon'
import ArrowLeftIcon from '../../icons/ArrowLeftIcon'
import UserIcon from '../../icons/UserIcon'
import styled from 'styled-components'
import useLocalization from '../../hooks/useLocalization'
import TicketRip from '../../components/TicketRip'
import TicketCard from '../../components/TicketCard'

export default function BookingStatus() {
    const{t, locale} = useLocalization();
    const { bookingId, queueType, branchId } = useParams()
    const PAGE_TITLE = t(`${queueType} Status`)

    const [rating, setRating] = useState(0)
    const [allowRate, setAllowRate] = useState(false)
    const [isShowDialog, setIsShowDialog] = useState(false)
    const [isShowPromotion, setIsShowPromotion] = useState(true)

    const promotionsQuery = usePromotions(branchId)
    const bookingQuery = useQuery('booking', () => getBooking(queueType, bookingId), {
        enabled: promotionsQuery.isSuccess
    })
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
    const service = serviceQuery.data
    const branch = branchQuery.data
    const termCondition = termConditionQuery.data
    let schedule = null;

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
            return el.start_time === booking?.start_time
        })

         if (bookingQuery.status === 'success' && branchQuery.status === 'success') {
        
                schedule = branch.schedule?.find(v => {
                    return v.day === getDayName(formatBrowser(booking?.date), 'en')
                })
            }

    const bookingStatusMap = {
        'book': t('Booked'),
        'check in': 'Check In',
        'served': t('Serve'),
        'end served': t('End Served'),
        'no show': t('No Show'),
        'canceled': t('Cancelled')
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
    const BookingTimeCard = styled.div`
        background: rgba(255, 255, 255, 0.04);
        box-shadow: 0px 7px 40px rgba(0, 0, 0, 0.1);
        border-radius: 12px;
        width: 86px;
        height: 86px;
        padding: .875rem;
        color: #007EC6;
    `
    function TicketHead(props) {
        const {t, locale} = useLocalization();
        return <> <div style={{
            paddingBottom:'2.5rem'
        }}>
            <div style={{
            padding: '1.75rem',
        }}>
                <div style={{
                display: 'flex',
                justifyContent:'space-between',
                marginBottom: '0.8rem',
                }}>
                    <h4 style={{
                        fontWeight: '700',
                        fontSize: '1.5rem',
                        color: '#103C7C',
                    }}>
                        {props?.serviceName}
                    </h4>
                    <p style={{
                        backgroundColor:'#92EC47',
                        color:'#fff',
                        borderRadius:'20px',
                        textAlign:'end',
                        fontSize: '1rem',
                        marginBottom: '.5rem',
                        padding:'0.5rem 1.2rem'
                    }}>{props?.session ? `Sesi ` + props?.session : '-'}</p>
                </div>

                <div style={{
                display: 'flex',
                justifyContent:'space-between',
                }}>
                    <p style={{
                        fontSize: '1rem',
                        color: '#7A7A7A',
                        marginBottom: '.5rem',
                    }}>{props?.date ? 
                        format(new Date(props?.date), 
                        "EEEE, dd MMMM yyyy")
                        : '-'}</p>
                    
                    <p style={{
                        fontSize: '1rem',
                        textAlign:'end',
                        marginBottom: '.5rem',
                    }}>{props?.startTime && props?.endTime ? 
                        `${props?.startTime} - ${props?.endTime}` 
                        : '-'}</p>
                </div>
            </div>


            <div style={{ 
                backgroundColor:'#e6f3ff',
                display: 'flex',
                justifyContent:'space-between',
                padding:'1rem 3rem'
             }}>
                <div style={{ textAlign:'center' }}>
                    <h4 style={{ marginBottom:'1.2rem' }}>Nomor Antrian</h4>
                    <h2 style={{ fontSize:'2.5rem' }}>{props?.queueNo || 0}</h2>
                </div>
                <div style={{ color:'blue', textAlign:'center' }}>
                    <h4 style={{ marginBottom:'1.2rem' }}>Antrian Sekarang</h4>
                    <h2 style={{ fontSize:'2.5rem' }}>{props?.currentQueue || 0}</h2>
                </div>
            </div>

        </div>
            </>
    }
    function TicketFooter(props) {
        return <div style={{
            display: 'flex',
            padding: '1.75rem',
            textAlign: 'center',
            flexDirection: 'column'
          }}>
            <div style={{ marginBottom: '1.2rem' }}>
              <h4 style={{ marginBottom: '0.3rem' }}>Kode Booking</h4>
              <p style={{ color: 'blue' }}>{props?.bookingCode}</p>
            </div>
            <div>
              <h4 style={{ marginBottom: '0.3rem' }}>Status Antrian</h4>
              <div style={{
                margin: '0 auto .5rem',
                padding: '3px 10px',
                display: 'inline-block'
              }}> 
              {['waiting', 'served', 'check in', 'book'].includes(props?.status) &&
                <ChipWarning label={bookingStatus} style={{  borderRadius: '20px', }} />}
                {props?.status === 'end served' && <ChipSuccess label={bookingStatus} style={{  borderRadius: '20px', }}/>}
                {['canceled', 'no show'].includes(props?.status) && <ChipDanger label={bookingStatus}  style={{  borderRadius: '20px', }}/>}</div>
            </div>
          </div>          
    }

    function handleCancelClick() {
        setIsShowDialog(true)
    }

    function handleConfirmCancel(id) {
        cancelAppointment(id)
        bookingQuery.refetch()
        setIsShowDialog(false)
    }

    function handleStoryDone() {
        setIsShowPromotion(false)
    }

    function createTermConditionMarkup() {
        return {__html: termCondition?.body}
    }

    const View = styled.div`
        width: 420px;
    `

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

        <Header>
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
                    <ArrowLeftIcon/>
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

            <TicketCard style={{ padding:'3rem 1.5rem 1rem 1.5rem' }}>
                <TicketHead 
                    serviceName={booking?.service_name}
                    session={slot?.session}
                    date={booking?.date}
                    startTime={booking?.start_time}
                    endTime={booking?.end_time}
                    queueNo={booking?.queue_no}
                    currentQueue={booking?.current_queue}
                />
                <TicketRip/>
                <TicketFooter
                    bookingCode={booking?.booking_code.toUpperCase()}
                    status={booking?.status}
                />
            </TicketCard>

        <div style={{
            padding: '1.625rem 1.375rem',
            zIndex: '10'
        }}>


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
                                    {booking?.email}
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
                                    {booking?.notes || '-'}
                                    </h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </Card>}

            {!!branchType && queueType === 'appointment' && booking?.status === 'end served' && <Card style={{
                margin: '1.625rem 0',
                padding: '1.625rem'
            }}>
                <p style={{
                    textAlign: 'center'
                }}>{t('How satisfied are you with our service?')}</p>

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
                    }} onClick={handleFeedbackClick}>{t('Send Feedback')}</button>
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
                }}>{t('Queue Information')}</h4>

                <p style={{
                    lineHeight: '1.5',
                }}>
                    {queueType === 'onsite'
                        ? t('You will be called by the counter you selected according to your queue number.')
                        : t('The queue can be viewed in your email. Just show your Queue Number according to the time you specified.')}
                </p>
            </InfoAlert>

            {termCondition?.body && <InfoAlert>
                <h4 style={{
                    fontSize: '1rem',
                    marginBottom: '.375rem',
                    textTransform: 'capitalize'
                }}>{t('Terms and Conditions')}</h4>

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
                {t('Cancel Appointment')}
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
                }}>{t('Are you sure you want to cancel the appointment?')}</p>
            </div>

            <div style={{
                display: 'flex',
                justifyContent: 'center',
                gap: '1rem'
            }}>
                <Button color="primary" onClick={() => handleConfirmCancel(booking?.id)}>{t('Yes, Cancel the Appointment')}</Button>
                <Button color="secondary" onClick={() => setIsShowDialog(false)}>{t('No')}</Button>
            </div>
        </Dialog>}
    </>
}
