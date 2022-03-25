import { useState, useEffect } from 'react'
import { useParams, Link } from 'react-router-dom'
import { useQuery, useQueryClient } from 'react-query'
import { format, getDayName, getMonthNames, getDayIndex } from '../../utils/date'
import { fetchBranch } from '../../api/branch'
import { fetchServiceByBranchId } from '../../api/services'

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

import AngleRightIcon from '../../icons/AngleRightIcon'
import CalendarIcon from '../../icons/CalendarIcon'
import ClockIcon from '../../icons/ClockIcon'

function ServiceList() {
    const { branchId, queueType } = useParams()
    const queryClient = useQueryClient()

    const PAGE_TITLE = `Booking ${queueType}`

    const [selectedDate, setSelectedDate] = useState(new Date())
    const [showCalendar, setShowCalendar] = useState(false)
    
    const branchRes = useQuery('branch', () => fetchBranch(branchId))
    const serviceRes = useQuery('services', () => fetchServiceByBranchId(branchId, {
        queueType,
        date: selectedDate
    }))

    let serviceCardList
    if (serviceRes.status === 'loading') serviceCardList = <div>Loading....</div>
    if (serviceRes.status === 'error') serviceCardList = <div>{serviceRes.error}</div>
    if (serviceRes.status === 'success') {
        const services = serviceRes.data

        serviceCardList = services.map(service => {
            let serviceProps = {}

            if (queueType == 'onsite') {
                serviceProps = {
                    title: service.service.name,
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
                    <span>{service.slots?.length} Sesi Waktu</span>
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

            return <Link to={`${service.id}?date=${selectedDate.toISOString()}`} key={service.id}>
                <ServiceItem
                    {...serviceProps}
                    style={{
                        marginBottom: '1.125rem'
                    }}
                />
            </Link>
        })
    }

    if (branchRes.status === 'error') return <div>{branchRes.error}</div>
    if (branchRes.status === 'loading') return <div>Loading...</div>
    if (branchRes.status === 'success') {
        const branch = branchRes.data
        const currentSchedule = branch?.schedule.find(v => (v.day === getDayName(new Date(), 'en')))

        function handleDayClick(day, modifiers = {}) {
            if (modifiers.disabled) return
            
            setSelectedDate(day)
        }

        function handleCalendarClose() {
            queryClient.invalidateQueries('services')
            setShowCalendar(false)
        }

        function getClosedDays() {
            return branch ? branch.schedule.filter(v => {
                return v.status === 'closed'
            }).map(val => {
                return getDayIndex(val.day)
            }) : []
        }

        return <>
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

                {currentSchedule && <BranchStatus
                    isOpen={currentSchedule.status == 'open'}
                    startTime={currentSchedule.start_time.slice(0, -3)}
                    endTime={currentSchedule.end_time.slice(0, -3)}
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
            </Banner>

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

                {serviceCardList}
            </MainContent>
        </>
    }
}

export default ServiceList