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
import MainContent from '../../components/MainContent'
import ArrowLeftIcon from '../../icons/ArrowLeftIcon'
import BranchNameWrapper from '../../components/style1/BranchNameWrapper'
import DetailWrapper from '../../components/style1/DetailWrapper'
import ServiceItemSkeleton from '../../components/style1/ServiceItemSkeletonStyle1'
import IndustryCategoryWrapper from '../../components/style1/IndustryCategoryWrapper'

import AngleRightIcon from '../../icons/AngleRightIcon'
import useBranchServicesCategories from '../../hooks/useBranchServiceCategories'

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
        navigate(`/customer/${branchId}/onsite/services`);
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
         <Banner imageUrl={branch?.photo} style={{ borderRadius:'0px 0px 0px 0px' }}>
                <SliderIndicator active={0} total={3} style={{
                    position: 'absolute',
                    bottom: '40%',
                    left: '50%',
                    transform: 'translateX(-50%)'
                }} />
        </Banner>

        <MainContent  style={{   position: 'absolute',
                                top: '25%',
                                width: '100%',
                                backgroundColor: 'white',
                                borderRadius: '20px 20px 0px 0px', }}>
            <div style={{ display:'flex', justifyContent: 'space-evenly',alignItems:'center',marginBottom:'2.5rem'  }}>
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
                            <div style={{ display:'flex', justifyContent: 'space-between',  alignItems: 'center', }}>
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
                          </div>
                        </div>
                        <hr style={{ marginBottom:'2rem' }}/>

            <h4 style={{
                fontSize: '1rem',
                marginBottom: '1.125rem'
            }}>{t('Service Category')}</h4>

            {branchServicesCategoryQuery.isLoading && <ServiceItemSkeleton />}

            {isBranchOpen() && categories?.map(category => {
                return <Link to={`/customer/${branchId}/onsite/two-layer/${category.id}/services`} key={category.id} style={{
                    marginBottom: '1.125rem'
                }}>
                    <Card style={{
                        display: 'flex',
                        height: '85px',
                        cursor: 'pointer',
                        backgroundImage: 'linear-gradient(270deg, #103C7C -24.91%, #2F5B9B 118.64%)',
                        color:'#fff'
                    }}>
                        <ServiceContent>
                            <ServiceTitle>{ category.name }</ServiceTitle>
                        </ServiceContent>
                    </Card>
                </Link>
            })}

            {categories && categories.length == 0 && isBranchOpen() && <div style={{
                    flex: '1 1 0%',
                    display: 'flex',
                    flexDirection: 'column',
                    justifyContent: 'center',
                    alignItems: 'center'
                }}>
                    <div style={{
                        display: 'inline-flex',
                        borderRadius: '99999999px',
                        backgroundColor: '#F5F5F5',
                        justifyContent: 'center',
                        alignItems: 'center',
                        height: '7.5rem',
                        width: '7.5rem',
                        marginBottom: '1.5rem'
                    }}>
                        <BoxOpenIcon width="5rem" height="5rem" color="#A5A5A5" />
                    </div>
                    <h4>{t('No Service Available')}</h4>
                    <p style={{
                        textAlign: 'center',
                        width: '280px',
                        marginTop: '0.5rem',
                        color: '#A5A5A5',
                        fontSize: '.875rem'
                    }}>
                        {t('Add Services in Admin Branch')}
                    </p>
                </div>}
        </MainContent>
    </>
}

export default ServiceCategories