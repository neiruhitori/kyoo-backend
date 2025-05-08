import { useState, useEffect } from 'react'
import { useNavigate, useParams, useSearchParams } from 'react-router-dom'
import { format, eachMonthOfInterval, parseISO } from 'date-fns'
import id from 'date-fns/locale/id'

import useBranch from '../../hooks/useBranch'
import useBranchSchedules from '../../hooks/useBranchSchedules'
import useBranchHolidays from '../../hooks/useBranchHolidays'
import useBranchServices from '../../hooks/useBranchServices'

import 'react-day-picker/lib/style.css'

import { Link } from 'react-router-dom'
import DayPicker from 'react-day-picker'

import Banner from '../../components/Banner'
import Header from '../../components/Header'
import Chip from '../../components/Chip'
import H2 from '../../components/H2'
import BranchStatusOpen from '../../components/style1/BranchStatusOpenStyle1'
import BranchStatusClosed from '../../components/style1/BranchStatusClosedStyle1'
import SliderIndicator from '../../components/SliderIndicator'
import MainContent from '../../components/style3/MainContent'
import ArrowLeftIcon from '../../icons/ArrowLeftIcon'
import BranchNameWrapper from '../../components/style1/BranchNameWrapper'
import DetailWrapper from '../../components/style1/DetailWrapper'
import ServiceItemSkeleton from '../../components/style3/ServiceItemSkeletonStyle3'
import IndustryCategoryWrapper from '../../components/style1/IndustryCategoryWrapper'

import AngleRightIcon from '../../icons/AngleRightIcon'
import useBranchServicesCategories from '../../hooks/useBranchServiceCategories'
import ProfileCard from '../../components/style3/ProfileCard'
import IconWrapper from '../../components/style3/IconWrapper'
import ClockIcon from '../../icons/ClockIcon'

import styled from 'styled-components'
import Card from '../../components/Card'

import useLocalization from '../../hooks/useLocalization'

function ServiceCategories() {
    const { branchId } = useParams()
    const [searchParams] = useSearchParams()
    const isAllowback = searchParams.get("is_allow_back")
    const navigate = useNavigate()

    const { t } = useLocalization(); 

    const PAGE_TITLE = t('Appointment Queue')

    const [selectedDate, setSelectedDate] = useState(new Date())
    const [isCalendarShow, setIsCalendarShow] = useState(false)

    const branchQuery = useBranch(branchId)
    const branchSchedulesQuery = useBranchSchedules(branchId)
    const branchHolidaysQuery = useBranchHolidays(branchId)
    const branchServicesCategoryQuery = useBranchServicesCategories(branchId)

    const branch = branchQuery?.data
    const schedules = branchSchedulesQuery?.data
    const holidays = branchHolidaysQuery?.data
    const categories = branchServicesCategoryQuery?.data

    const todaySchedule = schedules?.find(v => v.day === format(selectedDate, 'eeee').toLowerCase())
    const todayHoliday = holidays?.find(v => v.date === format(selectedDate, 'yyyy-MM-dd'))

    if(branch && branch.branch_configuration.layer === 1) {
        navigate(`/customer/${branchId}/appointment/services`);
    }

    function isBranchOpen() {
        return todaySchedule?.status === 'open' && !todayHoliday
    }

    const ServiceContent = styled.div(() => ({
        display: 'flex',
        flex: '1 1 0%',
        alignItems: 'center',
        justifyContent: 'center'
    }))

    const ServiceTitle = styled.div(() => ({
        fontWeight: '700',
        fontSize: '1rem'
    }))

    return <>
         <Banner imageUrl={branch?.photo} style={{ borderRadius:'0px' }}>
                 
                 <ProfileCard>
                     <div style={{ display:'flex', gap:'1.2rem', marginBottom:'1rem' }}>
                         <img src={branch?.logo ? `/storage/${branch?.logo}` : `/img/store.svg`}style={{ height:'4rem'}} />
                       <div>
                         <IndustryCategoryWrapper>
                                     {branch?.industry_category.name}
                         </IndustryCategoryWrapper>
                         <div>
                             <BranchNameWrapper>
                                 {branch?.name}
                             </BranchNameWrapper>
                         </div>
                       </div>
                     </div>
         
                     <hr style={{ marginBottom:'1.2rem' }}/>
                         
                     <div style={{ display:'flex', justifyContent: 'space-between', }}>
                             <div style={{ display:'flex', alignItems: 'center' }}>
                                 <IconWrapper>
                                     <ClockIcon fill='white'/>
                                 </IconWrapper>
                                 {isBranchOpen()
                                     ? <BranchStatusOpen
                                         startTime={todaySchedule?.start_time.slice(0, -3)}
                                         endTime={todaySchedule?.end_time.slice(0, -3)}
                                         style={{
                                             marginTop: '1rem'
                                         }}
                                         t={t}
                                     />
                                     : <BranchStatusClosed
                                         style={{
                                             marginTop: '1rem'
                                         }}
                                         t={t}
                                     />
                                 }
                             </div>
                             <Link to={`/customer/${branch?.id}/appointment/detail`} style={{ alignContent:'end' }}>
                                 <DetailWrapper>
                                     <span style={{padding: '0 0.75rem'}}>{t('See Details')}</span>
                                     <AngleRightIcon color="#33A0FF" style={{
                                         display:'block',
                                         margin:'auto',
                                         borderRadius:'50%',
                                         backgroundColor:'#fff',
                                         fill:'#33A0FF',
                                         padding:'3px'
                                     }}/>
                                 </DetailWrapper>
                             </Link>
                         </div>
                 </ProfileCard>
                </Banner>

        <MainContent  style={{ backgroundColor: '#103c7c'}}>
            <h4 style={{
                fontSize: '1rem',
                marginBottom: '1.125rem',
                color:'white'
            }}>{t('Service Category')}</h4>

            {branchServicesCategoryQuery.isLoading && <ServiceItemSkeleton />}

            {isBranchOpen() && categories?.map(category => {
                return <Link to={`/customer/${branchId}/appointment/${category.id}/services`} key={category.id} style={{
                    marginBottom: '1.125rem'
                }}>
                    <Card style={{
                        display: 'flex',
                        height: '75px',
                        cursor: 'pointer',
                        backgroundColor: '#33A0FF',
                        color:'#fff'
                    }}>
                        <ServiceContent>
                            <ServiceTitle>{ category.name }</ServiceTitle>
                        </ServiceContent>
                    </Card>
                </Link>
            })}
        </MainContent>
    </>
}

export default ServiceCategories

