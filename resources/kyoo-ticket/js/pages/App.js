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
                <Route path="/kyooTicket/:queueType/:branchId/services" element={<ServiceList />} />
                <Route path="/kyooTicket/:queueType/:branchId/services/:serviceId" element={<TimeSlotList />} />
                <Route path="/kyooTicket/:queueType/:branchId/services/:serviceId/visitor" element={<VisitorInformation />} />
                <Route path="/kyooTicket/:queueType/:branchId/services/:serviceId/booking-confirmation" element={<BookingConfirmation />} />
                <Route path="/kyooTicket/onsite/:branchId/booking-status/:bookingId" element={<OnsiteBookingStatus />} />
                <Route path="/kyooTicket/:queueType/:branchId/booking-status/:bookingId" element={<BookingStatus />} />
                <Route path="/kyooTicket/:queueType/:branchId/detail" element={<BranchDetail />} />
            </Routes>
        </QueryClientProvider>
    </AppContainer>
}

export default App