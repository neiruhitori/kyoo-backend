import { useState, useEffect } from 'react'
import { useNavigate, useParams, useSearchParams } from 'react-router-dom'
import { format, eachMonthOfInterval, parseISO } from 'date-fns'
import id from 'date-fns/locale/id'
import en from 'date-fns/locale/en-US'

import useBranch from '../../hooks/useBranch'
import useBranchSchedules from '../../hooks/useBranchSchedules'
import useBranchHolidays from '../../hooks/useBranchHolidays'
import useBranchServices from '../../hooks/useBranchServices'
import useBranchServicesCategories from '../../hooks/useBranchServiceCategories'

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
import TextField from '../../components/TextField'
import IconButton from '../../components/IconButton'
import ArrowLeftIcon from '../../icons/ArrowLeftIcon'
import CalendarWrapper from '../../components/CalendarWrapper'
import ServiceItemSkeleton from '../../components/ServiceItemSkeleton'
import ServiceItem from '../../components/ServiceItem'
import useLocalization from '../../hooks/useLocalization'
import styled from 'styled-components'

import AngleRightIcon from '../../icons/AngleRightIcon'
import CalendarIcon from '../../icons/CalendarIcon'
import ClockIcon from '../../icons/ClockIcon'
import BoxOpenIcon from '../../icons/BoxOpenIcon'
import { fetchBranch } from '../../api/branch'
import { useQuery } from 'react-query'
import { getDayName, getFullDate } from '../../utils/date'
import Card from '../../components/Card'


function CategoryServicesTwoLayer() {
    const { branchId, serviceCategoryId } = useParams()
    const [searchParams] = useSearchParams()
    const isAllowback = searchParams.get("is_allow_back")
    const navigate = useNavigate()
    const {t, locale} = useLocalization();
    const dateLocale = locale == "id" ? id : en;

    const ServiceTitle = styled.div(() => ({
            fontWeight: '700',
            fontSize: '1rem'
        }))

    const ServiceContent = styled.div(() => ({
            display: 'flex',
            flex: '1 1 0%',
            alignItems: 'center',
            justifyContent: 'center'
        }))

    const PAGE_TITLE = t('Appointment Queue')

    const [selectedDate, setSelectedDate] = useState(new Date())

    // const branchServicesQuery = useBranchServices(branchId, {
    //         queueType: 'appointment-onsite',
    //         date: getFullDate(selectedDate, 'en'),
    //         day: getDayName(selectedDate, 'en'),
    //     })
    const branchQuery = useQuery('branch', () => fetchBranch(branchId))
    const branchSchedulesQuery = useBranchSchedules(branchId)
    const branchHolidaysQuery = useBranchHolidays(branchId)
    const branchServicesCategoryQuery = useBranchServicesCategories(branchId)


    // const services = branchServicesQuery?.data
    const categories = branchServicesCategoryQuery?.data
    const branch = branchQuery?.data
    const schedules = branchSchedulesQuery?.data
    const holidays = branchHolidaysQuery?.data

    const todaySchedule = schedules?.find(v => v.day === format(selectedDate, 'eeee').toLowerCase())
    const todayHoliday = holidays?.find(v => v.date === format(selectedDate, 'yyyy-MM-dd'))

    if(branch && branch.branch_configuration.layer === 1){
        navigate(`/customer/${branchId}/onsite/services`);
    }

    // useEffect(() => {
    //     branchSchedulesQuery.refetch()
    //     branchHolidaysQuery.refetch()
    //     branchServicesCategoryQuery.refetch()
    // }, [selectedDate])

    function isBranchOpen() {
        return todaySchedule?.status === 'open' && !todayHoliday
    }
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

                <Link to={-1} style={{
                    display: 'flex',
                    justifyContent: 'center',
                    alignItems: 'center',
                    padding: '.85rem 1.375rem'
                }}>
                    <ArrowLeftIcon />
                </Link>

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

                    <Link to={`/customer/${branch?.id}/onsite/detail`}>
                        <div style={{
                            color: '#FFFFFF',
                            display: 'flex',
                            alignItems: 'center',
                            fontSize: '0.75rem',
                            textShadow: '0 1px 3px rgb(0 0 0 / 0.1), 0 1px 2px rgb(0 0 0 / 0.1)'
                        }}>
                            <span style={{
                                padding: '0 0.75rem'
                            }}>{t('See Details')}</span>

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
                        t={t}
                    />
                    : <BranchStatusClosed
                        style={{
                            marginTop: '1rem'
                        }}
                        t={t}
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
            }}>{t('Service')}</h4>

            {branchServicesCategoryQuery.isLoading && <h5>Loading...</h5>}

            
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

            {/* {isBranchOpen && services?.map(service => {
                    const availableSlot =
                        service.filledSlot < service.totalSlot
                        ? service.totalSlot - service.filledSlot
                        : 0;

                    if (!service.is_show || service.service_category_id) return null;

                    return (
                        <Link
                        to={`/customer/${branchId}/onsite/services/${service.id}?date=${format(
                            selectedDate,
                            'yyyy-MM-dd'
                        )}`}
                        key={service.id}
                        style={{ marginBottom: '1.125rem' }}
                        >
                        <ServiceItem
                            title={service.name}
                            action={{
                            label: t('Total Available Slots'),
                            value: availableSlot,
                            total: service.totalSlot,
                            }}
                            subtitle={
                            <div
                                style={{
                                display: 'flex',
                                alignItems: 'center',
                                }}
                            >
                                <ClockIcon
                                color="#A5A5A5"
                                width="0.75rem"
                                height="0.75rem"
                                style={{ marginRight: '0.5rem' }}
                                />
                                <span>
                                {service.slots.length
                                    ? service.slots.length + ` ${t('Time Sessions')}`
                                    : t('No Time Sessions')}
                                </span>
                            </div>
                            }
                        />
                        </Link>
                    )}
                )} */}

            {!isBranchOpen() && <div style={{
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
                <h4>{t('No Services')}</h4>
                <p style={{
                    textAlign: 'center',
                    width: '280px',
                    marginTop: '0.5rem',
                    color: '#A5A5A5',
                    fontSize: '.875rem'
                }}>
                    {t('Select another date to find available services')}
                </p>
            </div>}

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
                    <h4>Tidak Ada Kategori Layanan</h4>
                    <p style={{
                        textAlign: 'center',
                        width: '280px',
                        marginTop: '0.5rem',
                        color: '#A5A5A5',
                        fontSize: '.875rem'
                    }}>
                        Tambahkan Kategori Layanan di Admin Branch
                    </p>
                </div>}
        </MainContent>
    </>
}

export default CategoryServicesTwoLayer