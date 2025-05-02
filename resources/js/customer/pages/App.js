import { useState, useEffect } from 'react'
import styled from 'styled-components'
import { Routes, Route, useLocation } from 'react-router-dom'

import { onMessage } from 'firebase/messaging'
import { useMessaging, useToken } from '../lib/firebase'
import { getCookie } from '../lib/helper'

import QRReader from './QRReader/QRReader'

import ServiceList from './ServiceList/ServiceList'
import TimeSlotList from './TimeSlotList/TimeSlotList'
import OnsiteTimeSlotList from './onsite/TimeSlotList'
import VisitorInformation from './VisitorInformation/VisitorInformation'
import BookingConfirmation from './BookingConfirmation/BookingConfirmation'
import BookingStatus from './BookingStatus/BookingStatus'
import OnsiteBookingStatus from './OnsiteBookingStatus/OnsiteBookingStatus'
import AppointmentOnsiteBookingStatus from './AppointmentOnsiteBookingStatus/AppointmentOnsiteBookingStatus'
import OnsiteServicesTwoLayer from './onsite/ServicesTwoLayer'
import BranchDetail from './BranchDetail/BranchDetail'
import OnsiteVisitorInformation from './OnsiteVisitorInformation/OnsiteVisitorInformation'
import AppointmentOnsiteVisitorInformation from './AppointmentOnsiteVisitorInformation/AppointmentOnsiteVisitorInformation'
import BookingDetail from './BookingDetail/BookingDetail'
import AppointmentServicesCategories from './appointment/ServiceCategories'
import AppointmentServices from './appointment/Services'
import AppointmentServicesTwoLayer from './appointment/ServicesTwoLayer'
import Promotions from './Promotions/Promotions'

import InfoAlert from '../components/InfoAlert'

import RouteAppointmentStyle1 from './Route/Style1'
import RouteAppointmentStyle2 from './Route/Style2'
import RouteAppointmentStyle3 from './Route/Style3'

const AppContainer = styled.div`
    max-width: 420px;
    margin: 0 auto;
    background-color: #FFFFFF;
    position: relative;
    min-height: ${window.innerHeight}px;
    display: flex;
    flex-direction: column;
`

function App() {
    const CLIENT_ID = getCookie('client_id')
    const webStyle = document.getElementById('root').getAttribute('wstyle') ?? 'web-style-1'
    const [infoMessasge, setInfoMessage] = useState('')

    // const messaging = useMessaging()
    // useToken(messaging, process.env.MIX_FIREBASE_VAPID_KEY)

    useEffect(() => {
        // onMessage(messaging, ({ data }) => {
        //     handleShowNotification(data.body)
        // })

        window.Echo.channel(`onsite_queues.${CLIENT_ID}`)
            .listen('OnsiteQueueCalled', ({ message }) => {
                handleShowNotification(message)
            })

        return () => {
            window.Echo.leave(`onsite_queues.${CLIENT_ID}`)
        }
    }, [])

    function handleShowNotification(message) {
        setInfoMessage(message)

        setTimeout(function () {
            setInfoMessage('')
        }, 5000)
    }
    function renderPageByStyle(style) {
        switch (style) {
            case 'web-style-1':
                return <RouteAppointmentStyle1 />;
            case 'web-style-2':
                return <RouteAppointmentStyle2 />;
            case 'web-style-3':
                return <RouteAppointmentStyle3 />;
            default:
                return <RouteAppointmentStyle1 />;
        }
    }

    return <AppContainer webStyle={webStyle}>
        {!!infoMessasge && <div style={{
            backgroundColor: 'rgba(0, 0, 0, 0.5)',
            position: 'fixed',
            top: 0,
            right: 0,
            bottom: 0,
            left: 0,
            zIndex: 9999
        }}>
            <InfoAlert style={{
                width: 'max-content',
                maxWidth: '382px',
                margin: '0 auto',
                marginTop: '2rem'
            }}>
                {infoMessasge}
            </InfoAlert>
        </div>}

            { renderPageByStyle(webStyle) }
        
        {/* <Routes>
            <Route path="/scan" element={<QRReader />} />

            <Route path="/customer/:branchId/appointment/services" element={<AppointmentServices />} />
            <Route path="/customer/:branchId/appointment/:serviceCategoryId/services" element={<AppointmentServices />} />
            <Route path="/customer/:branchId/appointment/services/two-layer" element={<AppointmentServicesCategories />} />
            <Route path="/customer/:branchId/:queueType/services" element={<ServiceList />} />
            <Route path="/customer/:branchId/onsite/services/two-layer" element={<OnsiteServicesTwoLayer />} />
            <Route path="/customer/:branchId/onsite/services/:serviceId" element={<OnsiteTimeSlotList />} />
            <Route path="/customer/:branchId/:queueType/services/:serviceId" element={<TimeSlotList />} />
            <Route path="/customer/:branchId/:queueType/services/:serviceId/visitor" element={<VisitorInformation />} />
            <Route path="/customer/:branchId/appointment-onsite/services/:serviceId/visitor" element={<AppointmentOnsiteVisitorInformation />} />
            <Route path="/customer/:branchId/onsite/services/:serviceId/visitor" element={<OnsiteVisitorInformation />} />
            <Route
                path="/customer/:branchId/:queueType/services/:serviceId/booking-confirmation"
                element={<BookingConfirmation />}
            />
            <Route
                path="/customer/:branchId/appointment-onsite/booking-status/:bookingId"
                element={<AppointmentOnsiteBookingStatus />}
            />
            <Route
                path="/customer/:branchId/onsite/booking-status/:bookingId"
                element={<OnsiteBookingStatus />}
            />
            <Route path="/customer/:branchId/:queueType/booking-status/:bookingId" element={<BookingStatus />} />
            <Route path="/customer/:branchId/:queueType/booking-status/:bookingId/detail" element={<BookingDetail />} />
            <Route path="/customer/:branchId/:queueType/detail" element={<BranchDetail />} />
            <Route path="/customer/:branchId/:queueType/promotions" element={<Promotions />} />
        </Routes> */}
    </AppContainer>
}

export default App
