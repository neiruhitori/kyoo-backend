import { useState } from 'react'
import styled from 'styled-components'
import { Routes, Route } from 'react-router-dom'
import { QueryClient, QueryClientProvider } from 'react-query'

import { onMessage } from 'firebase/messaging'
import { useMessaging } from '../lib/firebase'

import QRReader from './QRReader/QRReader'

import ServiceList from './ServiceList/ServiceList'
import TimeSlotList from './TimeSlotList/TimeSlotList'
import VisitorInformation from './VisitorInformation/VisitorInformation'
import BookingConfirmation from './BookingConfirmation/BookingConfirmation'
import BookingStatus from './BookingStatus/BookingStatus'
import OnsiteBookingStatus from './OnsiteBookingStatus/OnsiteBookingStatus'
import BranchDetail from './BranchDetail/BranchDetail'
import OnsiteVisitorInformation from './OnsiteVisitorInformation/OnsiteVisitorInformation'
import InfoAlert from '../components/InfoAlert'

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
    const [infoMessasge, setInfoMessage] = useState('')

    const messaging = useMessaging()
    onMessage(messaging, ({ data }) => {
        setInfoMessage(data.body)

        setTimeout(function () {
            setInfoMessage('')
        }, 5000)
    })

    return <AppContainer>
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
        
        <QueryClientProvider client={new QueryClient()}>
            <Routes>
                <Route path="/scan" element={<QRReader />} />

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
                <Route path="/customer/:branchId/:queueType/detail" element={<BranchDetail />} />
            </Routes>
        </QueryClientProvider>
    </AppContainer>
}

export default App