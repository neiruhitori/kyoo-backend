import styled from 'styled-components'
import { Routes, Route } from 'react-router-dom'
import { QueryClient, QueryClientProvider } from 'react-query'
import { ReactQueryDevtools } from 'react-query/devtools'

import ServiceList from './ServiceList/ServiceList'
import TimeSlotList from './TimeSlotList/TimeSlotList'

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
            </Routes>

            <ReactQueryDevtools initialIsOpen />
        </QueryClientProvider>
    </AppContainer>
}

export default App