import { useState, useMemo, useRef } from 'react'
import QRCode from 'react-qr-code';
import { useParams, Link } from 'react-router-dom'
import { useQuery, useMutation } from 'react-query'
import { getBooking, cancelBooking } from '../../api/booking'
import { fetchServiceById } from '../../api/services'
import { createFeedback, fetchSurvey } from '../../api/feedback'
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
import TicketFooter from '../../components/TicketStyle5'
import SurveyRenderer from './../../components/SurveyRenderer'

export default function BookingStatus() {
    const{t, locale} = useLocalization();
    const { bookingId, queueType, branchId } = useParams()
    const PAGE_TITLE = t(`${queueType} Status`)

    const [rating, setRating] = useState({})
    const [allowRate, setAllowRate] = useState(false)
    const [isShowDialog, setIsShowDialog] = useState(false)
    const [isShowPromotion, setIsShowPromotion] = useState(true)
    const [isFeedbackSubmitted, setIsFeedbackSubmitted] = useState(false)
    const ticketRef = useRef(null)

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
            setRating(booking.rating || {})
            setAllowRate(!booking.rating)
        }
    }, [booking])
    


    function handleFeedbackClick() {
        feedbackMutation.mutate({
            rating,
            is_liked: false
        }, {
            onSuccess: ({ data }) => {
                setIsFeedbackSubmitted(true) 
                booking.rating = data.rating
            }
        })
    }
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
                        fontSize: '6rem',
                        color: '#103C7C',
                        marginBottom:'1rem'
                    }}>
                        {props?.queue}
                    </h4>
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

        {booking && branch?.id && (
                <TicketCard style={{ margin:'5rem 2rem' }}>
                    <TicketHead
                        branchName={branch?.name}
                        queue={booking?.queue_no}
                    />
                    <TicketRip/>
                    <TicketFooter 
                        booking={booking}
                    />
                </TicketCard>
            )}


        <div style={{
            padding: '1.625rem 1.375rem',
            zIndex: '10'
        }}>

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
