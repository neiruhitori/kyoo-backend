import { useState, useMemo } from 'react'
import { useParams, Link } from 'react-router-dom'
import { useQuery, useMutation } from 'react-query'
import { getBooking, cancelBooking } from '../../api/booking'
import { fetchServiceById } from '../../api/services'
import { createFeedback, fetchSurvey } from '../../api/feedback'
import { fetchBranch } from '../../api/branch'
import { getTermConditionByBranchId } from '../../api/termCondition'
import { cancelAppointment } from '../../api/appointment'
import usePromotions from '../../hooks/usePromotions'
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
import Story from '../../components/Story'

import ClockIcon from '../../icons/ClockIcon'
import ArrowLeftIcon from '../../icons/ArrowLeftIcon'
import styled from 'styled-components'
import useLocalization from '../../hooks/useLocalization'
import SurveyRenderer from './../../components/SurveyRenderer';

export default function BookingStatus() {
    const{t, locale} = useLocalization();
    const { bookingId, queueType, branchId } = useParams()
    const PAGE_TITLE = t(`${queueType} Status`)

    const [rating, setRating] = useState({})
    const [allowRate, setAllowRate] = useState(false)
    const [isShowDialog, setIsShowDialog] = useState(false)
    const [isShowPromotion, setIsShowPromotion] = useState(true)
    const [isFeedbackSubmitted, setIsFeedbackSubmitted] = useState(false)

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
    const surveyQuery = useQuery('survey', () => fetchSurvey(branchId), {
        enabled: bookingQuery.status === 'success'
    })

    const feedbackMutation = useMutation('feedback', (data) => createFeedback(queueType, bookingId, data))
    const termConditionQuery = useQuery('termCondition', () => getTermConditionByBranchId(branchId))

    const booking = bookingQuery.data?.data
    const service = serviceQuery.data
    const branch = branchQuery.data
    const termCondition = termConditionQuery.data
    const surveyData = surveyQuery.data?.data


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
            setRating(booking.rating || {})
            setAllowRate(!booking.rating)
        }
    }, [booking])

    function handleFeedbackClick() {
        feedbackMutation.mutate({
            rating,
            is_liked: false,
            bookingId
        }, {
            onSuccess: ({ data }) => {
                setIsFeedbackSubmitted(true) 
                booking.rating = data.rating
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
                                {!!booking && getAbrvDate(formatBrowser(booking.date), locale)}
                            </span>
                            <span style={{
                                margin: '0 0.5rem'
                            }}>|</span>
                            <span style={{
                                marginRight: '0.5rem'
                            }}>{t('Session')} {slot?.session}:</span>
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
                                {t('Booking Code')}
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
                                {t('Queue Status')}
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
                         {t('Queue Number')}
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
                       {t('Current Queue')}
                    </div>

                    <span>{booking?.current_queue || 0}</span>
                </BlueCard>
            </Card>

            {!!booking?.name && <Card style={{
                marginTop: '1.625rem'
            }}>
                <h4 style={{
                    marginBottom: '1.625rem'
                }}>{t('Customer Details')}</h4>

                <div>
                    <div style={{
                        display: 'flex',
                        fontSize: '.875rem',
                        marginBottom: '1.125rem'
                    }}>
                        <div style={{
                            flex: '1',
                            color: '#A5A5A5'
                        }}>{t('Full Name')}</div>

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
                        }}>{t('Phone Number')}</div>

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
                        }}>{t('Notes')}</div>

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

            {!!branchType && queueType === 'appointment' && booking?.status === 'end served' && 
                    <SurveyRenderer
                            surveyData={surveyData}
                            booking={booking}
                            rating={rating}
                            setRating={setRating}
                            handleFeedbackClick={handleFeedbackClick}
                            isFeedbackSubmitted = {isFeedbackSubmitted}
                            t={t}
                        />}

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
