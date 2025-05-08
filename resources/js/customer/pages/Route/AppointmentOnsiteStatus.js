import React from 'react';

import AppointmentOnsiteBookingStatus from '../AppointmentOnsiteBookingStatus/AppointmentOnsiteBookingStatus';
import AppointmentOnsiteBookingStatus2 from '../AppointmentOnsiteBookingStatus/AppointmentOnsiteBookingStatusStyle2';
import AppointmentOnsiteBookingStatus3 from '../AppointmentOnsiteBookingStatus/AppointmentOnsiteBookingStatusStyle3';
import AppointmentOnsiteBookingStatus4 from '../AppointmentOnsiteBookingStatus/AppointmentOnsiteBookingStatusStyle4';
import AppointmentOnsiteBookingStatus5 from '../AppointmentOnsiteBookingStatus/AppointmentOnsiteBookingStatusStyle5';

import useBranch from '../../hooks/useBranch'
import { useParams } from 'react-router-dom';

const AppointmentOnsiteTicket = () => {
  const { branchId } = useParams();
  const branchQuery = useBranch(branchId);
  const ticketStyle = branchQuery?.data?.branch_configuration?.ticket_style;

  if (branchQuery.isLoading) return <div>Loading...</div>;

  if (branchQuery.isError) return <div>Error loading branch config</div>;
  
  const ApptOnsiteComponents = {
    'ticket-style-2': AppointmentOnsiteBookingStatus2,
    'ticket-style-3': AppointmentOnsiteBookingStatus3,
    'ticket-style-4': AppointmentOnsiteBookingStatus4,
    'ticket-style-5': AppointmentOnsiteBookingStatus5,
  };
  
  
  const SelectedComponent = ApptOnsiteComponents[ticketStyle] || AppointmentOnsiteBookingStatus;
  
  return <SelectedComponent />;
};

export default AppointmentOnsiteTicket;
