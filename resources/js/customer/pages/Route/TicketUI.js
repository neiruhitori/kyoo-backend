import React from 'react';
import BookingStatus from '../BookingStatus/BookingStatus'
import BookingStatusStyle2 from '../BookingStatus/BookingStatusStyle2'
import BookingStatusStyle3 from '../BookingStatus/BookingStatusStyle3'
import BookingStatusStyle4 from '../BookingStatus/BookingStatusStyle4'
import BookingStatusStyle5 from '../BookingStatus/BookingStatusStyle5'
import useBranch from '../../hooks/useBranch'
import { useParams } from 'react-router-dom';

const TicketUI = () => {
  const { branchId } = useParams();
  const branchQuery = useBranch(branchId);
  const ticketStyle = branchQuery?.data?.branch_configuration?.ticket_style;
  const isAppt = branchQuery?.data?.branch_type?.is_appointment;

  if (branchQuery.isLoading) return <div>Loading...</div>;

  if (branchQuery.isError) return <div>Error loading branch config</div>;

  const ApptComponents = {
    'ticket-style-2': BookingStatusStyle2,
    'ticket-style-3': BookingStatusStyle3,
    'ticket-style-4': BookingStatusStyle4,
    'ticket-style-5': BookingStatusStyle5,
  };
  
  const OnsiteComponents = {
    'ticket-style-2': BookingStatusStyle2,
    'ticket-style-3': BookingStatusStyle3,
    'ticket-style-4': BookingStatusStyle4,
    'ticket-style-5': BookingStatusStyle5,
  };
  
  const DefaultComponent = BookingStatus;
  
  const SelectedComponent = isAppt
    ? (ApptComponents[ticketStyle] || DefaultComponent)
    : (OnsiteComponents[ticketStyle] || DefaultComponent);
  
  return <SelectedComponent />;
};

export default TicketUI;
