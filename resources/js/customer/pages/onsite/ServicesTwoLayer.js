import { useState, useEffect } from 'react'
import { useNavigate, useParams } from 'react-router-dom'
import { format } from 'date-fns'
import id from 'date-fns/locale/id'

import useBranchHolidays from '../../hooks/useBranchHolidays'

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
import BoxOpenIcon from '../../icons/BoxOpenIcon'
import styled from 'styled-components'
import Card from '../../components/Card'
import CheckIcon from '../../icons/CheckIcon'
import { fetchServiceByBranchId } from '../../api/services'
import { fetchAppointmentOnsiteSlots } from '../../api/appointmentOnsite'
import { useQuery } from 'react-query'
import { getDayName, getFullDate, getMonthNames } from '../../utils/date'
import { fetchBranch } from '../../api/branch'
import SkeletonItem from '../../components/SkeletonItem'

function ServicesTwoLayer() {
    const { branchId } = useParams()
    const queueType = 'onsite'
    const navigate = useNavigate()

    const today = new Date();
    const date = new Date(today);
    date.setDate(today.getDate() + 1);

    const [showCalendar, setShowCalendar] = useState(false)
    const [selectedDate, setSelectedDate] = useState(date)
    const [serviceId, setServiceId] = useState()

    const branchRes = useQuery('branch', () => fetchBranch(branchId))
    const branchHolidaysQuery = useBranchHolidays(branchId)
    const servicesRes = useQuery('services',
        () => fetchServiceByBranchId(branchId, {
            queueType
        })
    )
    const serviceSlotsRes = useQuery('services-slots',
        () => fetchAppointmentOnsiteSlots(branchId, {
            date: getFullDate(selectedDate, 'en'),
            day: getDayName(selectedDate, 'en'),
            serviceId: serviceId
        })
    )

    let branch = null,
        holidays = [],
        todayHoliday = null,
        schedule = null,
        services = [],
        serviceSlots = []

    function isBranchOpen() {
        return schedule?.status === 'open' && !todayHoliday
    }

    function isSameDay() {
        return format(selectedDate, 'yyyy-MM-dd') === format(today, 'yyyy-MM-dd')
    }

    if (branchRes.status === 'success') {
        branch = branchRes.data
        schedule = branch?.schedule.find(v => (v.day === getDayName(selectedDate, 'en')))
    }

    if(branchHolidaysQuery.status === 'success') {
        holidays = branchHolidaysQuery.data
        todayHoliday = holidays?.find(v => v.date === format(selectedDate, 'yyyy-MM-dd'))
    }

    if (servicesRes.status === 'success') {
        services = servicesRes.data
    }

    if (serviceSlotsRes.status === 'success') {
        serviceSlots = serviceSlotsRes.data
    }

    if(branch && branch.branch_configuration.layer === 1){
        navigate(`/customer/${branchId}/onsite/services`);
    }

    useEffect(() => {
        servicesRes.refetch()
        serviceSlotsRes.refetch()
    }, [serviceId, selectedDate])

    useEffect(() => {
        const service = services.find(service => service.is_show);
        setServiceId(service?.id);
    }, [services])

    // EVENTS
    function handleDayClick(day, modifiers = {}) {
        if (modifiers.disabled) return
        setSelectedDate(day)
    }

    // METHODS
    function getClosedDays() {
        return branch ? branch.schedule.filter(v => {
            return v.status === 'closed'
        }).map(val => {
            return getDayIndex(val.day)
        }) : []
    }

    const ContentWrapper = styled.div`
        width: 420px;
        overflow-y: scroll;
        max-height: 100vh;
        scrollbar-width: none;
        -ms-overflow-style: none;
        &::-webkit-scrollbar {
            display: none;
        }
        `;

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
            display: 'flex'
         }}>
            <ContentWrapper>
                <Banner imageUrl={branch?.photo}>
                    <Header>
                        <div style={{
                            borderLeft: '1px solid #EEEEEE',
                            textTransform: 'capitalize',
                            padding: '0 1.375rem 0 .85rem',
                            flex: '1'
                        }}>
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

                            <Link to={`/customer/${branchId}/onsite/detail`}>
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
                                startTime={schedule?.start_time.slice(0, -3)}
                                endTime={schedule?.end_time.slice(0, -3)}
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
                    }}>Layanan</h4>

                    {servicesRes.isLoading &&
                        <Card style={{
                            borderLeft: '12px solid #007EC6',
                            height: '80px'
                        }}>
                            <SkeletonItem height="1rem" width="100px" />
                            <SkeletonItem height=".75rem" style={{
                                marginTop: '1rem'
                            }} />
                        </Card>
                    }

                    {isBranchOpen() && services?.map(service => {
                        if (!service.is_show) return;

                        return <span style={{marginBottom: '1.125rem'}} key={service.id}>
                                    <Card style={{
                                        display: 'flex',
                                        height: '85px',
                                        cursor: 'pointer',
                                        backgroundColor: serviceId == service.id ? '#1f4b8b' : '',
                                        color: serviceId == service.id ? 'white' : 'black',
                                    }} onClick={() => setServiceId(service.id)}>
                                        <ServiceContent>
                                            <ServiceTitle>{ service.name }</ServiceTitle>
                                            <CheckIcon />
                                        </ServiceContent>
                                    </Card>
                                </span>
                    })}

                    {!servicesRes.isLoading && !isBranchOpen() && <div style={{
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
            </ContentWrapper>

            <div style={{ backgroundColor: '#E2E2E2', height: '100vh', width: '100px' }}></div>

            <ContentWrapper>
                <MainContent>
                    {showCalendar && <CalendarWrapper onClick={() => setShowCalendar(false)}>
                        <DayPicker
                            onDayClick={handleDayClick}
                            months={getMonthNames()}
                            modifiers={{
                                selected: selectedDate,
                                disabled: [
                                    { daysOfWeek: getClosedDays() },
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
                                onClick={() => setShowCalendar(true)}
                            >
                                <CalendarIcon height="24" width="24" />
                            </IconButton>
                        }
                    />

                    <div style={{
                        padding: '1.5rem 1.375rem',
                        backgroundColor: '#FFFFFF',
                        margin: '0 -1.375rem',
                        borderRadius: '16px 16px 0 0',
                        boxShadow: '0px -4px 40px rgba(0, 0, 0, 0.13)',
                        flex: '1 1 0%',
                        display: 'flex',
                        flexDirection: 'column'
                    }}>
                        <div style={{
                            marginBottom: '1.125rem'
                        }}>
                            <h4 style={{
                                fontSize: '1rem',
                                marginBottom: '0.5rem'
                            }}>Daftar Sesi Waktu</h4>
                            <p style={{
                                fontSize: '0.875rem',
                                color: '#A5A5A5'
                            }}>Berikut adalah sesi waktu yang tersedia</p>
                        </div>

                        {serviceSlotsRes.isLoading && <ServiceItemSkeleton />}

                        {!isSameDay() && isBranchOpen() && serviceSlotsRes.status === 'success' && serviceId && serviceSlots?.map(serviceSlot => {
                            return <Link to={`/customer/${branchId}/appointment-onsite/services/${serviceId}/visitor?date=${format(selectedDate, 'yyyy-MM-dd')}&start_time=${serviceSlot.start_time}&end_time=${serviceSlot.end_time}`} key={serviceSlot.start_time} style={{
                                marginBottom: '1.125rem'
                            }}>
                                <ServiceItem
                                    title={`${serviceSlot.start_time} - ${serviceSlot.end_time}`}
                                    action={{
                                        label: "Total Slot Tersedia",
                                        value: serviceSlot.available_slots,
                                        total: serviceSlot.max_slots
                                    }}
                                    style={{
                                        alignItems: 'center'
                                    }}
                                />
                            </Link>
                        })}

                        {!serviceSlotsRes.isLoading && !isSameDay() && !isBranchOpen() && <div style={{
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

                        {isSameDay() && <div style={{
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
                                Anda harus melakukan pemesanan antrian setidaknya 1 hari sebelumnya.
                            </p>
                        </div>}
                    </div>
                </MainContent>
            </ContentWrapper>
        </div>
    </>
}

export default ServicesTwoLayer
