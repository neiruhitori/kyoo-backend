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
import BranchStatusOpen from '../../components/BranchStatusOpen'
import BranchStatusClosed from '../../components/BranchStatusClosed'
import SliderIndicator from '../../components/SliderIndicator'
import MainContent from '../../components/MainContent'
import ArrowLeftIcon from '../../icons/ArrowLeftIcon'
import ServiceItemSkeleton from '../../components/ServiceItemSkeleton'

import AngleRightIcon from '../../icons/AngleRightIcon'
import useBranchServicesCategories from '../../hooks/useBranchServiceCategories'

import styled from 'styled-components'
import Card from '../../components/Card'

function ServiceCategories() {
    const { branchId } = useParams()
    const [searchParams] = useSearchParams()
    const isAllowback = searchParams.get("is_allow_back")
    const navigate = useNavigate()

    const PAGE_TITLE = 'Antrian Appointment'

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

    if(branch && branch.branch_configuration.layer === 2) {
        navigate(`/customer/${branchId}/appointment/services/two-layer`);
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
        <Banner imageUrl={branch?.photo}>
            <Header>
                {
                    isAllowback ?
                        <div style={{
                            display: 'flex',
                            height: '100%'
                        }}>
                            <div
                                onClick={() => history.back()}
                                style={{
                                    justifyContent: 'center',
                                    display: 'flex',
                                    alignItems: 'center',
                                    padding: '.85rem 1.375rem',
                                }}
                            >
                                <ArrowLeftIcon/>
                            </div>
                        </div>
                    :
                        ""
                }


                <div style={{
                    borderLeft: '1px solid #EEEEEE',
                    textTransform: 'capitalize',
                    padding: '0 1.375rem 0 .85rem',
                    flex: '1'
                }}>{PAGE_TITLE}</div>

                <div style={{margin:'0 10px'}}>
                    <a href="#" style={{
                        display: 'flex',
                        alignItems: 'center'
                    }}>
                        <img src={branch?.logo ? `/storage/${branch?.logo}` : `/img/logo-color.svg`} height="26" />
                    </a>
                </div>
            </Header>

            <div style={{
                padding: '1.625rem 1.375rem'
            }}>
                <div style={{
                    display: 'flex',
                    alignItems: 'center',
                    justifyContent: 'space-between',
                    marginBottom: '1rem'
                }}>
                    <Chip label={branch?.industry_category.name} />

                    <Link to={`/customer/${branch?.id}/appointment/detail`}>
                        <div style={{
                            color: '#FFFFFF',
                            display: 'flex',
                            alignItems: 'center',
                            fontSize: '0.75rem',
                            textShadow: '0 1px 3px rgb(0 0 0 / 0.1), 0 1px 2px rgb(0 0 0 / 0.1)'
                        }}>
                            <span style={{
                                padding: '0 0.75rem'
                            }}>Lihat Detail</span>

                            <AngleRightIcon color="#FFFFFF" />
                        </div>
                    </Link>
                </div>

                <div>
                    <H2 style={{
                        color: '#FFFFFF'
                    }}>{branch?.name}</H2>
                </div>

                {isBranchOpen()
                    ? <BranchStatusOpen
                        startTime={todaySchedule?.start_time.slice(0, -3)}
                        endTime={todaySchedule?.end_time.slice(0, -3)}
                        style={{
                            marginTop: '1rem'
                        }}
                    />
                    : <BranchStatusClosed
                        style={{
                            marginTop: '1rem'
                        }}
                    />
                }

                <SliderIndicator active={0} total={3} style={{
                    position: 'absolute',
                    bottom: '1rem',
                    left: '50%',
                    transform: 'translateX(-50%)'
                }} />
            </div>
        </Banner>

        <MainContent>
            <h4 style={{
                fontSize: '1rem',
                marginBottom: '1.125rem'
            }}>Kategori Layanan</h4>

            {branchServicesCategoryQuery.isLoading && <ServiceItemSkeleton />}

            {isBranchOpen() && categories?.map(category => {
                return <Link to={`/customer/${branchId}/appointment/${category.id}/services`} key={category.id} style={{
                    marginBottom: '1.125rem'
                }}>
                    <Card style={{
                        display: 'flex',
                        height: '85px',
                        cursor: 'pointer',
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

