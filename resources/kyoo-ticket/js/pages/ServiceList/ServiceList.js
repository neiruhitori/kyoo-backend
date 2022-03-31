import { useState } from 'react'
import { useParams, Link, useNavigate } from 'react-router-dom'
import { useQuery, useMutation } from 'react-query'
import { format, getDayName, getMonthNames, getDayIndex, getFullDate } from '../../utils/date'
import { fetchBranch } from '../../api/branch'
import { fetchServiceByBranchId } from '../../api/services'
import { createBooking } from '../../api/booking'

import 'react-day-picker/lib/style.css'

import DayPicker from 'react-day-picker';
import Header from '../../components/Header'
import Banner from '../../components/Banner'
import KyooLogo from "../../components/KyooLogo"
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
import Loading from '../../components/Loading'
import Alert from '../../components/Alert'

import AngleRightIcon from '../../icons/AngleRightIcon'
import CalendarIcon from '../../icons/CalendarIcon'
import ClockIcon from '../../icons/ClockIcon'

function ServiceList() {
    const { branchId, queueType } = useParams()
    const PAGE_TITLE = `Booking ${queueType}`
    const navigate = useNavigate()

    const [selectedDate, setSelectedDate] = useState(new Date())
    const [showCalendar, setShowCalendar] = useState(false)

    const branchRes = useQuery('branch', () => fetchBranch(branchId))
    const servicesRes = useQuery(['services', selectedDate], () => fetchServiceByBranchId(branchId, {
        queueType,
        date: selectedDate
    }))
    const bookingMutation = useMutation('booking', data => createBooking(queueType, data))

    let branch = null
    let schedule = null
    let services = []

    if (branchRes.status === 'success') {
        branch = branchRes.data
        schedule = branch?.schedule.find(v => (v.day === getDayName(new Date(), 'en')))
    }
    if (servicesRes.status === 'success') {
        services = servicesRes.data
    }

    function handleServiceClick(serviceId) {
        bookingMutation.mutate({
            workstation_service_id: serviceId
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
        return branch ? branch.schedule.filter(v => {
            return v.status === 'closed'
        }).map(val => {
            return getDayIndex(val.day)
        }) : []
    }

    if (bookingMutation.status === 'success' && bookingMutation.data.success) {
        navigate(`/kyooTicket/${queueType}/${branchId}/booking-status/${bookingMutation.data.data.id}`)
    }

    return <>
        {bookingMutation.status === 'loading' && <Loading />}

        <Header>
            <div style={{
                paddingRight: '0.5rem',
                borderRight: '1px solid #EEEEEE',
                marginRight:' 0.75rem'
            }}>
                <a href="#">
                    <KyooLogo />
                </a>
            </div>

            <div style={{ textTransform: 'capitalize' }}>{PAGE_TITLE}</div>
        </Header>

        <Banner imageUrl="/img/queue.jpeg">
            {branchRes.status === 'success' && <div>
                <div style={{
                    display: 'flex',
                    alignItems: 'center',
                    justifyContent: 'space-between',
                    marginBottom: '1rem'
                }}>
                    <Chip label={branch?.industry_category.name} />

                    <a href="#">
                        <div style={{
                            color: '#FFFFFF',
                            display: 'flex',
                            alignItems: 'center',
                            fontSize: '0.75rem',
                        }}>
                            <span style={{
                                padding: '0 0.75rem'
                            }}>Lihat Detail</span>
                            <AngleRightIcon color="#FFFFFF" />
                        </div>
                    </a>
                </div>

                <div style={{
                    width: '224px'
                }}>
                    <H2 style={{
                        color: '#FFFFFF'
                    }}>{branch?.name}</H2>
                </div>

                {schedule && <BranchStatus
                    isOpen={schedule.status == 'open'}
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
            </div>}
        </Banner>

        <MainContent>
            {bookingMutation.status === 'success' && !bookingMutation.data.success && <Alert style={{
                marginBottom: '1.5rem'
            }}>Gagal membuat antrian: {bookingMutation.data.message}</Alert>}

            {showCalendar && <CalendarWrapper
                onClick={handleCalendarClose}
            >
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
                let serviceProps = {}

                if (queueType == 'onsite') {
                    serviceProps = {
                        title: service.service.name,
                        key: service.workstation_service_id,
                        action: {
                            label: 'Total Antrian',
                            value: service.total_queue
                        }
                    }
                } else {
                    const serviceSubtitle = <div style={{
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
                    </div>

                    serviceProps = {
                        title: service.name,
                        subtitle: serviceSubtitle,
                        action: {
                            label: "Total Slot Tersedia",
                            value: service.totalSlot - service.filledSlot,
                            total: service.totalSlot
                        }
                    }
                }

                if (queueType == 'onsite') {
                    return <ServiceItem
                        {...serviceProps}
                        onClick={() => handleServiceClick(service.workstation_service_id)}
                        style={{
                            marginBottom: '1.125rem'
                        }}
                    />
                } else {
                    return <Link to={`${service.id}?date=${getFullDate(selectedDate)}`} key={service.id} style={{
                        marginBottom: '1.125rem'
                    }}>
                        <ServiceItem
                            {...serviceProps}
                        />
                    </Link>
                }
            })}
        </MainContent>
    </>
}

export default ServiceList