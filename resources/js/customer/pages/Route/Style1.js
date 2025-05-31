import { Routes, Route } from 'react-router-dom'

import OnsiteBookingStatus from '../OnsiteBookingStatus/OnsiteBookingStatus'
import AppointmentOnsiteBookingStatus from '../AppointmentOnsiteBookingStatus/AppointmentOnsiteBookingStatus'
import OnsiteServicesTwoLayer from '../onsite/ServicesTwoLayer'
import BranchDetail from '../BranchDetail/BranchDetail'
import OnsiteVisitorInformation from '../OnsiteVisitorInformation/OnsiteVisitorInformation'
import AppointmentOnsiteVisitorInformation from '../AppointmentOnsiteVisitorInformation/AppointmentOnsiteVisitorInformation'
import BookingDetail from '../BookingDetail/BookingDetail'
import AppointmentServicesCategories from '../appointment/ServiceCategories'
import ServiceList from '../ServiceList/ServiceList'
import TimeSlotList from '../TimeSlotList/TimeSlotList'
import OnsiteTimeSlotList from '../onsite/TimeSlotList'
import VisitorInformation from '../VisitorInformation/VisitorInformation'
import BookingConfirmation from '../BookingConfirmation/BookingConfirmation'
import BookingStatus from '../BookingStatus/BookingStatus'
import QRReader from '../QRReader/QRReader'
import AppointmentServices from '../appointment/Services'
import AppointmentServicesTwoLayer from '../appointment/ServicesTwoLayer'
import Promotions from '../Promotions/Promotions'
import CategoryOnsiteServicesTwoLayer from '../onsite/OnsiteCategoryService'
import TicketUI from './TicketUI'
import AppointmentOnsiteTicket from './AppointmentOnsiteStatus';

import { useParams } from 'react-router-dom';



export default function AppointmentStyle1() {
const ticketStyle = document.getElementById('root').getAttribute('tstyle') ?? 'ticket_style_1'
  return (
   <Routes>
               <Route path="/scan" element={<QRReader />} />
               <Route path="/customer/:branchId/appointment/services" element={<AppointmentServices />} />
               <Route path="/customer/:branchId/appointment/:serviceCategoryId/services" element={<AppointmentServices />} />
               <Route path="/customer/:branchId/appointment/services/two-layer" element={<AppointmentServicesCategories />} />
               <Route path="/customer/:branchId/:queueType/services" element={<ServiceList />} />
               <Route path="/customer/:branchId/onsite/services/two-layer" element={<CategoryOnsiteServicesTwoLayer/>} />
               <Route path="/customer/:branchId/onsite/two-layer/:serviceCategoryId/services" element={<OnsiteServicesTwoLayer />} />
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
                   element={<AppointmentOnsiteTicket />}
               />
               <Route
                   path="/customer/:branchId/onsite/booking-status/:bookingId"
                   element={<TicketUI />}
               />
               <Route path="/customer/:branchId/:queueType/booking-status/:bookingId" element={<TicketUI />} />
               <Route path="/customer/:branchId/:queueType/booking-status/:bookingId/detail" element={<BookingDetail />} />
               <Route path="/customer/:branchId/:queueType/detail" element={<BranchDetail />} />
               <Route path="/customer/:branchId/:queueType/promotions" element={<Promotions />} />
           </Routes>
  )
}
