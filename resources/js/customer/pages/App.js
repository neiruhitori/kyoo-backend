import styled from 'styled-components'
import { Routes, Route } from 'react-router-dom'
import { QueryClient, QueryClientProvider } from 'react-query'

import ServiceList from './ServiceList/ServiceList'
import TimeSlotList from './TimeSlotList/TimeSlotList'
import VisitorInformation from './VisitorInformation/VisitorInformation'
import BookingConfirmation from './BookingConfirmation/BookingConfirmation'
import BookingStatus from './BookingStatus/BookingStatus'
import OnsiteBookingStatus from './OnsiteBookingStatus/OnsiteBookingStatus'
import BranchDetail from './BranchDetail/BranchDetail'

const AppContainer = styled.div`
    max-width: 420px;
    margin: 0 auto;
    background-color: #FFFFFF;
    position: relative;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
`

function App() {
    return <AppContainer>
        <QueryClientProvider client={new QueryClient()}>
            <Routes>
                <Route path="/customer/:branchId/:queueType" element={<ServiceList />} />
                <Route path="/customer/:branchId/:queueType/services" element={<ServiceList />} />
                <Route path="/customer/:branchId/:queueType/services/:serviceId" element={<TimeSlotList />} />
                <Route path="/customer/:branchId/:queueType/services/:serviceId/visitor" element={<VisitorInformation />} />
                <Route path="/customer/:branchId/:queueType/services/:serviceId/booking-confirmation" element={<BookingConfirmation />} />
                <Route path="/customer/:branchId/onsite/booking-status/:bookingId" element={<OnsiteBookingStatus />} />
                <Route path="/customer/:branchId/:queueType/booking-status/:bookingId" element={<BookingStatus />} />
                <Route path="/customer/:branchId/:queueType/detail" element={<BranchDetail />} />
            </Routes>
        </QueryClientProvider>
    </AppContainer>
}

export default App