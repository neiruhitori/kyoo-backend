import { useState, useEffect } from 'react'
import styled from 'styled-components'
import { Routes, Route, useLocation } from 'react-router-dom'

import { onMessage } from 'firebase/messaging'
import { useMessaging, useToken } from '../lib/firebase'
import { getCookie } from '../lib/helper'

import QRReader from './QRReader/QRReader'

import ServiceList from './ServiceList/ServiceList'
import TimeSlotList from './TimeSlotList/TimeSlotList'
import VisitorInformation from './VisitorInformation/VisitorInformation'
import BookingConfirmation from './BookingConfirmation/BookingConfirmation'
import BookingStatus from './BookingStatus/BookingStatus'
import OnsiteBookingStatus from './OnsiteBookingStatus/OnsiteBookingStatus'
import BranchDetail from './BranchDetail/BranchDetail'
import OnsiteVisitorInformation from './OnsiteVisitorInformation/OnsiteVisitorInformation'
import BookingDetail from './BookingDetail/BookingDetail'
import AppointmentServicesCategories from './appointment/ServiceCategories'
import AppointmentServices from './appointment/Services'
import AppointmentServicesTwoLayer from './appointment/ServicesTwoLayer'
import Promotions from './Promotions/Promotions'

import InfoAlert from '../components/InfoAlert'

const AppContainer = styled.div`
    width: 420px;
    margin: 0 auto;
    background-color: #FFFFFF;
    position: relative;
    min-height: ${window.innerHeight}px;
    display: flex;
    flex-direction: column;
    min-width: ${(props) => (props.isTwoLayer ? "fit-content" : "auto")};
`

function App() {
    const CLIENT_ID = getCookie('client_id')
    const [isTwoLayer, setIsTwoLayer] = useState(false)
    const location = useLocation();

    useEffect(() => {
        const branchIdMatch = location.pathname.match(/\/customer\/(\d+)\/appointment\/services\/two-layer/);

        if (branchIdMatch) {
            setIsTwoLayer(true);
        } else {
            setIsTwoLayer(false);
        }
    }, [location]);

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

    return <AppContainer isTwoLayer={isTwoLayer}>
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

        <Routes>
            <Route path="/scan" element={<QRReader />} />

            <Route path="/customer/:branchId/appointment/services" element={<AppointmentServicesCategories />} />
            <Route path="/customer/:branchId/appointment/:serviceCategoryId/services" element={<AppointmentServices />} />
            <Route path="/customer/:branchId/appointment/services/two-layer" element={<AppointmentServicesTwoLayer />} />
            <Route path="/customer/:branchId/:queueType/services" element={<ServiceList />} />
            <Route path="/customer/:branchId/:queueType/services/:serviceId" element={<TimeSlotList />} />
            <Route path="/customer/:branchId/:queueType/services/:serviceId/visitor" element={<VisitorInformation />} />
            <Route path="/customer/:branchId/onsite/services/:serviceId/visitor" element={<OnsiteVisitorInformation />} />
            <Route
                path="/customer/:branchId/:queueType/services/:serviceId/booking-confirmation"
                element={<BookingConfirmation />}
            />
            <Route
                path="/customer/:branchId/onsite/booking-status/:bookingId"
                element={<OnsiteBookingStatus />}
            />
            <Route path="/customer/:branchId/:queueType/booking-status/:bookingId" element={<BookingStatus />} />
            <Route path="/customer/:branchId/:queueType/booking-status/:bookingId/detail" element={<BookingDetail />} />
            <Route path="/customer/:branchId/:queueType/detail" element={<BranchDetail />} />
            <Route path="/customer/:branchId/:queueType/promotions" element={<Promotions />} />
        </Routes>
    </AppContainer>
}

export default App
