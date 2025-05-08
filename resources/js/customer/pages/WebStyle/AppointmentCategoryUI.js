import React from 'react';
import AppointmentServices from '../appointment/Services';
import useBranch from '../../hooks/useBranch'
import { useParams } from 'react-router-dom';
import ServiceCategories from './../appointment/ServiceCategories';
import ServiceCategoriesStyle1 from './../appointment/ServiceCategoriesStyle1';

const AppointmentCategoryUI = () => {
  const { branchId } = useParams();
  const branchQuery = useBranch(branchId);
  const webStyle = branchQuery?.data?.branch_configuration?.web_style;

  if (branchQuery.isLoading) return <div>Loading...</div>;

  if (branchQuery.isError) return <div>Error loading branch config</div>;

  switch (webStyle) {
    case 'web-style-2':
      return <ServiceCategoriesStyle1 />;
    default:
      return <ServiceCategories />;
  }
};

export default AppointmentCategoryUI;
