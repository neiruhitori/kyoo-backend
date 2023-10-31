import { useState, useEffect } from 'react'
import { useNavigate, useParams, useSearchParams } from 'react-router-dom'
import { format, eachMonthOfInterval, parseISO } from 'date-fns'
import id from 'date-fns/locale/id'

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

import AngleRightIcon from '../../icons/AngleRightIcon'
import CalendarIcon from '../../icons/CalendarIcon'
import ClockIcon from '../../icons/ClockIcon'
import BoxOpenIcon from '../../icons/BoxOpenIcon'
import styled from 'styled-components'
import Card from '../../components/Card'
import CheckIcon from '../../icons/CheckIcon'

function ServicesTwoLayer() {
    const { branchId } = useParams()
    const [searchParams] = useSearchParams();
    const isAllowback = searchParams.get("is_allow_back");
    const navigate = useNavigate();

    const PAGE_TITLE = 'Antrian Appointment'

    const [selectedDate, setSelectedDate] = useState(new Date())
    const [isCalendarShow, setIsCalendarShow] = useState(false)
    const [serviceCategoryId, setServiceCategoryId] = useState(0)

    const branchQuery = useBranch(branchId)
    const branchSchedulesQuery = useBranchSchedules(branchId)
    const branchHolidaysQuery = useBranchHolidays(branchId)
    const branchServicesCategoryQuery = useBranchServicesCategories(branchId)
    const branchServicesQuery = useBranchServices(branchId, {
        queueType: 'appointment',
        date: format(selectedDate, 'yyyy-MM-dd'),
        serviceCategoryId: serviceCategoryId
    })

    const branch = branchQuery?.data
    const schedules = branchSchedulesQuery?.data
    const holidays = branchHolidaysQuery?.data
    const categories = branchServicesCategoryQuery?.data
    const services = branchServicesQuery?.data

    const todaySchedule = schedules?.find(v => v.day === format(selectedDate, 'eeee').toLowerCase())
    const todayHoliday = holidays?.find(v => v.date === format(selectedDate, 'yyyy-MM-dd'))

    if(branch && branch.branch_configuration.layer === 1){
        navigate(`/customer/${branchId}/appointment/services`);
    }

    useEffect(() => {
        branchSchedulesQuery.refetch()
        branchHolidaysQuery.refetch()
        branchServicesQuery.refetch()
        branchServicesCategoryQuery.refetch()
    }, [selectedDate])

    useEffect(() => {
        branchServicesQuery.refetch()
    }, [serviceCategoryId])

    function isBranchOpen() {
        return todaySchedule?.status === 'open' && !todayHoliday
    }

    function getClosedDayIndexes() {
        const days = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday']

        return schedules
            .filter(v => v.status === 'closed')
            .map(v => days.indexOf(v.day))
    }

    function getMonthNames() {
        const now = new Date()

        return eachMonthOfInterval({
            start: new Date(now.getFullYear(), 0, 1),
            end: new Date(now.getFullYear(), 11, 1)
        })
            .map(v => {
                return format(v, 'MMMM', { locale: id })
            })
    }

    function handleDayClick(day, modifiers = {}) {
        if (modifiers.disabled) return
        setSelectedDate(day)
    }

    function handleCalendarClose() {
        setIsCalendarShow(false)
    }

    const ServiceContent = styled.div(() => ({
        display: 'flex',
        flex: '1 1 0%',
        alignItems: 'center',
        justifyContent: 'space-between'
    }))

    const ServiceTitle = styled.div(() => ({
        fontWeight: '700',
        fontSize: '1rem'
    }))

    return <>
        <div style={{
            display: 'flex',
         }}>
            <div style={{
                width: '420px',
             }}>
                <Banner imageUrl={branch?.photo}>
                    <Header>
                        {isAllowback ?
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
                        return <span style={{marginBottom: '1.125rem'}} key={category.id}>
                                    <Card style={{
                                        display: 'flex',
                                        height: '85px',
                                        cursor: 'pointer',
                                        backgroundColor: serviceCategoryId == category.id ? '#1f4b8b' : '',
                                        color: serviceCategoryId == category.id ? 'white' : 'black',
                                    }} onClick={() => setServiceCategoryId(category.id)}>
                                        <ServiceContent>
                                            <ServiceTitle>{ category.name }</ServiceTitle>
                                            <CheckIcon />
                                        </ServiceContent>
                                    </Card>
                                </span>
                    })}

                    {isBranchOpen() && categories &&
                        <span style={{marginBottom: '1.125rem'}}>
                            <Card style={{
                                display: 'flex',
                                height: '85px',
                                cursor: 'pointer',
                                backgroundColor: serviceCategoryId == null ? '#1f4b8b' : '',
                                color: serviceCategoryId == null ? 'white' : 'black',
                            }} onClick={() => setServiceCategoryId(null)}>
                                <ServiceContent>
                                    <ServiceTitle>Lainnya</ServiceTitle>
                                    <CheckIcon />
                                </ServiceContent>
                            </Card>
                        </span>
                    }

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
                        <h4>Tidak Ada Layanan</h4>
                        <p style={{
                            textAlign: 'center',
                            width: '280px',
                            marginTop: '0.5rem',
                            color: '#A5A5A5',
                            fontSize: '.875rem'
                        }}>
                            Pilih tanggal lain untuk menemukan layanan yang tersedia
                        </p>
                    </div>}
                </MainContent>
            </div>

            <div style={{ backgroundColor: '#E2E2E2', height: '100vh', width: '100px' }}></div>

            <div style={{
                width: '420px'
             }}>
                <Header>
                    {isAllowback ?
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

                <MainContent>
                    {isCalendarShow && <CalendarWrapper
                        onClick={handleCalendarClose}
                    >
                        <DayPicker
                            onDayClick={handleDayClick}
                            months={getMonthNames()}
                            modifiers={{
                                selected: selectedDate,
                                disabled: [
                                    ...holidays.map(v => parseISO(v.date)),
                                    { daysOfWeek: getClosedDayIndexes() },
                                    { before: new Date() }
                                ]
                            }}
                            modifiersStyles={{
                                selected: {
                                    backgroundColor: '#0172CB',
                                    color: '#FFFFFF'
                                }
                            }}
                        />
                    </CalendarWrapper>}

                    <TextField
                        label="Tanggal"
                        style={{
                            marginBottom: '1.5rem'
                        }}
                        value={format(selectedDate, 'd MMMM yyyy', { locale: id })}
                        readOnly
                        endAdornment={
                            <IconButton
                                onClick={() => setIsCalendarShow(true)}
                            >
                                <CalendarIcon height="24" width="24" />
                            </IconButton>
                        }
                    />

                    <h4 style={{
                        fontSize: '1rem',
                        marginBottom: '1.125rem'
                    }}>Layanan</h4>

                    {branchServicesQuery.isLoading && <ServiceItemSkeleton />}

                    {isBranchOpen() && services?.map(service => {
                        const availableSlot = service.filledSlot < service.totalSlot
                            ? service.totalSlot - service.filledSlot
                            : 0

                        if (!service.is_show) return

                        return <Link to={`/customer/${branchId}/appointment/services/${service.id}?date=${format(selectedDate, 'yyyy-MM-dd')}`} key={service.id} style={{
                            marginBottom: '1.125rem'
                        }}>
                            <ServiceItem
                                title={service.name}
                                action={{
                                    label: "Total Slot Tersedia",
                                    value: availableSlot,
                                    total: service.totalSlot
                                }}
                                subtitle={<div style={{
                                    display: 'flex',
                                    alignItems: 'center'
                                }}>
                                    <ClockIcon
                                        color="#A5A5A5"
                                        width="0.75rem"
                                        height="0.75rem"
                                        style={{
                                            marginRight: '0.5rem'
                                        }}
                                />
                                    <span>
                                        {
                                            service.slots.length
                                                ? service.slots.length + ' Sesi Waktu'
                                                : 'Tidak Ada Sesi Waktu'
                                        }
                                    </span>
                                </div>}
                            />
                        </Link>
                    })}

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
                        <h4>Tidak Ada Layanan</h4>
                        <p style={{
                            textAlign: 'center',
                            width: '280px',
                            marginTop: '0.5rem',
                            color: '#A5A5A5',
                            fontSize: '.875rem'
                        }}>
                            Pilih tanggal lain untuk menemukan layanan yang tersedia
                        </p>
                    </div>}

                    {services && services.length == 0 && isBranchOpen() && <div style={{
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
                        <h4>Tidak Ada Layanan</h4>
                        <p style={{
                            textAlign: 'center',
                            width: '280px',
                            marginTop: '0.5rem',
                            color: '#A5A5A5',
                            fontSize: '.875rem'
                        }}>
                            Pilih kategori lain untuk menemukan layanan yang tersedia
                        </p>
                    </div>}
                </MainContent>
            </div>
        </div>
    </>
}

export default ServicesTwoLayer
