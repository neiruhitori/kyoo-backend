import { useState } from 'react'
import { useParams, Link } from 'react-router-dom'
import { useQuery } from 'react-query'
import { format, getDayName, getMonthNames, getDayIndex, getFullDate, formatBrowser } from '../../utils/date'
import { fetchBranch } from '../../api/branch'
import { fetchServiceByBranchId } from '../../api/services'
import { fetchSchedulesByBranchId } from '../../api/schedules'
import { fetchHolidaysByBranchId } from '../../api/holidays'

import 'react-day-picker/lib/style.css'

import DayPicker from 'react-day-picker';
import Header from '../../components/Header'
import Banner from '../../components/Banner'
import Chip from '../../components/Chip'
import H2 from '../../components/H2'
import BranchStatus from '../../components/BranchStatus'
import SliderIndicator from '../../components/SliderIndicator'
import CalendarWrapper from '../../components/CalendarWrapper'
import TextField from '../../components/TextField'
import IconButton from '../../components/IconButton'
import ServiceItem from '../../components/ServiceItem'
import MainContent from '../../components/MainContent'
import ServiceItemSkeleton from '../../components/ServiceItemSkeleton'

import AngleRightIcon from '../../icons/AngleRightIcon'
import CalendarIcon from '../../icons/CalendarIcon'
import ClockIcon from '../../icons/ClockIcon'

function ServiceList() {
    const { branchId, queueType } = useParams()
    const PAGE_TITLE = `Antrian ${queueType}`

    const [selectedDate, setSelectedDate] = useState(new Date())
    const [showCalendar, setShowCalendar] = useState(false)

    const branchRes = useQuery('branch', () => fetchBranch(branchId))

    const servicesRes = useQuery(['services', selectedDate], () => fetchServiceByBranchId(branchId, {
        queueType,
        date: getFullDate(selectedDate)
    }))

    const schedulesRes = useQuery(['schedules', branchId], () => fetchSchedulesByBranchId(branchId))

    const holidaysRes = useQuery(['branch.holidays', branchId], () => fetchHolidaysByBranchId(branchId))

    let branch = null,
        schedule = null,
        services = [],
        schedules = [],
        holidays = []

    if (branchRes.status === 'success') {
        branch = branchRes.data
        schedule = branch?.schedule.find(v => (v.day === getDayName(new Date(), 'en')))
    }

    if (servicesRes.status === 'success') {
        services = servicesRes.data
    }

    if (schedulesRes.status === 'success') {
        schedules = schedulesRes.data
    }

    if (holidaysRes.status === 'success') {
        holidays = holidaysRes.data.map(v => {
            return formatBrowser(v.date)
        })
    }

    function handleDayClick(day, modifiers = {}) {
        if (modifiers.disabled) return
        setSelectedDate(day)
    }

    function handleCalendarClose() {
        setShowCalendar(false)
    }

    function getClosedDays() {
        return schedules.filter(v => v.status === 'closed')
            .map(v => {
                return getDayIndex(v.day)
            })
    }

    return <>
        {branchRes.status === 'success' && <Banner imageUrl={branch.photo}>
            <Header>
                <div style={{
                    display: 'flex',
                    height: '100%'
                }}>
                    <a href="#" style={{
                        padding: '.5rem .85rem .5rem 1.375rem',
                        display: 'flex',
                        alignItems: 'center'
                    }}>
                        <img src={branch.logo ? `/storage/${branch.logo}` : `/img/logo-color.svg`} height="26" />
                    </a>
                </div>

                <div style={{
                    borderLeft: '1px solid #EEEEEE',
                    textTransform: 'capitalize',
                    padding: '0 1.375rem 0 .85rem',
                    flex: '1'
                }}>{PAGE_TITLE}</div>
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

                    <Link to={`/customer/${branchId}/${queueType}/detail`}>
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

                {schedule && <BranchStatus
                    isOpen={schedule.status !== 'closed'}
                    startTime={schedule.start_time.slice(0, -3)}
                    endTime={schedule.end_time.slice(0, -3)}
                    style={{
                        marginTop: '1rem'
                    }}
                />}

                <SliderIndicator active={0} total={3} style={{
                    position: 'absolute',
                    bottom: '1rem',
                    left: '50%',
                    transform: 'translateX(-50%)'
                }} />
            </div>
        </Banner>}

        <MainContent>
            {showCalendar && <CalendarWrapper
                onClick={handleCalendarClose}
            >
                <DayPicker
                    onDayClick={handleDayClick}
                    months={getMonthNames()}
                    modifiers={{
                        selected: selectedDate,
                        disabled: [
                            ...holidays,
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

            {queueType != 'onsite' && <TextField
                label="Tanggal"
                style={{
                    marginBottom: '1.5rem'
                }}
                value={format(selectedDate)}
                readOnly
                endAdornment={
                    <IconButton
                        onClick={() => setShowCalendar(true)}
                    >
                        <CalendarIcon height="24" width="24" />
                    </IconButton>
                }
            />}

            <h4 style={{
                fontSize: '1rem',
                marginBottom: '1.125rem'
            }}>Layanan</h4>

            {servicesRes.status === 'loading' && <ServiceItemSkeleton />}

            {servicesRes.status === 'success' && services.map(service => {
                if (queueType == 'onsite') {
                    const serviceProps = {
                        title: service.name,
                        key: service.id,
                        action: {
                            label: 'Total Antrian',
                            value: service.total_queue
                        }
                    }

                    return <Link to={`${service.id}/visitor`} key={service.id} style={{
                        marginBottom: '1.125rem'
                    }}>
                        <ServiceItem {...serviceProps} />
                    </Link>
                }

                const serviceProps = {
                    title: service.name,
                    action: {
                        label: "Total Slot Tersedia",
                        value: service.totalSlot - service.filledSlot,
                        total: service.totalSlot
                    }
                }

                return <Link to={`${service.id}?date=${getFullDate(selectedDate)}`} key={service.id} style={{
                    marginBottom: '1.125rem'
                }}>
                    <ServiceItem
                        {...serviceProps}
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
        </MainContent>
    </>
}

export default ServiceList