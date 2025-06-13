import { Routes, Route } from 'react-router-dom'


import OnsiteServicesTwoLayerStyle2 from '../onsite/ServicesTwoLayerStyle2'
import BranchDetailStyle1 from '../BranchDetail/BranchDetailStyle1'
import OnsiteVisitorInformation from '../OnsiteVisitorInformation/OnsiteVisitorInformation'
import AppointmentOnsiteVisitorInformationStyle2 from '../AppointmentOnsiteVisitorInformation/AppointmentOnsiteVisitorInformationStyle2'
import BookingDetail from '../BookingDetail/BookingDetail'
import AppointmentServicesCategoriesStyle1 from '../appointment/ServiceCategoriesStyle1'
import ServiceList from '../ServiceList/ServiceList'
import TimeSlotList from '../TimeSlotList/TimeSlotList'
import OnsiteTimeSlotList from '../onsite/TimeSlotList'
import VisitorInformationStyle1 from '../VisitorInformation/VisitorInformationStyle1'
import BookingConfirmationStyle1 from '../BookingConfirmation/BookingConfirmationStyle1'
import BookingStatus from '../BookingStatus/BookingStatus'
import QRReader from '../QRReader/QRReader'
import AppointmentServicesStyle1 from '../appointment/ServicesStyle1'
import AppointmentServicesTwoLayer from '../appointment/ServicesTwoLayer'
import Promotions from '../Promotions/Promotions'
import TicketUI from './TicketUI'
import AppointmentOnsiteTicket from './AppointmentOnsiteStatus';
import CategoryOnsiteServicesTwoLayerStyle2 from './../onsite/OnsiteCategoryServiceStyle2'

import { useParams } from 'react-router-dom';



export default function AppointmentStyle1() {

  return (
   <Routes>
               <Route path="/scan" element={<QRReader />} />
               <Route path="/customer/:branchId/appointment/services" element={<AppointmentServicesStyle1 />} />
               <Route path="/customer/:branchId/appointment/:serviceCategoryId/services" element={<AppointmentServicesStyle1 />} />
               <Route path="/customer/:branchId/appointment/services/two-layer" element={<AppointmentServicesCategoriesStyle1 />} />
               <Route path="/customer/:branchId/:queueType/services" element={<ServiceList />} />
               <Route path="/customer/:branchId/onsite/services/two-layer" element={<CategoryOnsiteServicesTwoLayerStyle2/>} />
               <Route path="/customer/:branchId/onsite/two-layer/:serviceCategoryId/services" element={<OnsiteServicesTwoLayerStyle2 />} />
               <Route path="/customer/:branchId/onsite/services/:serviceId" element={<OnsiteTimeSlotList />} />
               <Route path="/customer/:branchId/:queueType/services/:serviceId" element={<TimeSlotList />} />
               <Route path="/customer/:branchId/:queueType/services/:serviceId/visitor" element={<VisitorInformationStyle1 />} />
               <Route path="/customer/:branchId/appointment-onsite/services/:serviceId/visitor" element={<AppointmentOnsiteVisitorInformationStyle2 />} />
               <Route path="/customer/:branchId/onsite/services/:serviceId/visitor" element={<OnsiteVisitorInformation />} />
               <Route
                   path="/customer/:branchId/:queueType/services/:serviceId/booking-confirmation"
                   element={<BookingConfirmationStyle1 />}
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
               <Route path="/customer/:branchId/:queueType/detail" element={<BranchDetailStyle1 />} />
               <Route path="/customer/:branchId/:queueType/promotions" element={<Promotions />} />
           </Routes>
  )
}
