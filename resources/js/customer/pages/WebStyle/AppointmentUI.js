import React from 'react';
import AppointmentServices from '../appointment/Services';
import AppointmentServicesStyle1 from '../appointment/ServicesStyle1';
import useBranch from '../../hooks/useBranch'
import { useParams } from 'react-router-dom';

const AppointmentUI = () => {
  const { branchId } = useParams();
  const branchQuery = useBranch(branchId);
  const webStyle = branchQuery?.data?.branch_configuration?.web_style;

  if (branchQuery.isLoading) return <div>Loading...</div>;

  if (branchQuery.isError) return <div>Error loading branch config</div>;

  switch (webStyle) {
    case 'web-style-2':
      return <AppointmentServicesStyle1 />;
    default:
      return <AppointmentServices />;
  }
};

export default AppointmentUI;
