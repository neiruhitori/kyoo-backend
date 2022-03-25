import { Link, useParams, useSearchParams } from 'react-router-dom'
import { useState } from 'react'
import { format, getMonthNames, getDayIndex, getDayName } from '../../utils/date'
import { useQuery } from 'react-query'
import { fetchBranch } from '../../api/branch'
import { fetchServiceById } from '../../api/services'

import DayPicker from 'react-day-picker';
import Header from '../../components/Header'
import MainContent from '../../components/MainContent'
import TextField from '../../components/TextField'
import IconButton from '../../components/IconButton'
import CalendarWrapper from '../../components/CalendarWrapper'
import Card from '../../components/Card'

import ArrowLeftIcon from '../../icons/ArrowLeftIcon'
import CalendarIcon from '../../icons/CalendarIcon'
import ClockIcon from '../../icons/ClockIcon'
import AngleRightIcon from '../../icons/AngleRightIcon'
import UserIcon from '../../icons/UserIcon'

function TimeSlotList(props) {
    const PAGE_TITLE = 'Slot Waktu'
    const { branchId, serviceId } = useParams()
    const [searchParams] = useSearchParams()

    const branchRes = useQuery('branch', () => fetchBranch(branchId))
    const serviceRes = useQuery('service', () => fetchServiceById(serviceId))

    const [showCalendar, setShowCalendar] = useState(false)
    const [selectedDate, setSelectedDate] = useState(new Date(searchParams.get('date')))
    let branch = null
    let service = null
    let slots = []

    if (branchRes.status === 'success') {
        branch = branchRes.data
    }
    if (serviceRes.status === 'success') {
        service = serviceRes.data
        slots = service.slot.filter(v => {
            return v.day === getDayName(selectedDate, 'en')
        })
    }

    const currentSchedule = branch?.schedule.find(v => (v.day === getDayName(selectedDate, 'en')))

    // EVENTS
    function handleDayClick(day, modifiers = {}) {
        if (modifiers.disabled) {
            return
        }
        
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

    return <>
        <Header>
            <div style={{
                marginRight:' 0.75rem'
            }}>
                <Link to={-1} style={{
                    display: 'flex',
                    justifyContent: 'center',
                    alignItems: 'center'
                }}>
                    <ArrowLeftIcon />
                </Link>
            </div>

            <div style={{ textTransform: 'capitalize' }}>{PAGE_TITLE}</div>
        </Header>

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
                value={format(selectedDate)}
                readOnly
                endAdornment={
                    <IconButton
                        onClick={() => setShowCalendar(true)}
                    >
                        <CalendarIcon height="24" width="24" />
                    </IconButton>
                }
            />

            {slots.length && <Card style={{
                marginBottom: '1.5rem'
            }}>
                <div style={{
                    fontWeight: '700',
                    fontSize: '1rem'
                }}>
                    {service.name}
                </div>
                <div style={{
                    fontSize: '0.75rem',
                    color: '#A5A5A5',
                    marginTop: '0.75rem',
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
                    <span>{slots.length} Sesi Waktu Tersedia</span>
                    <span style={{
                        margin: '0 0.5rem'
                    }}>|</span>
                    <span style={{
                        fontWeight: '700',
                        color: '#007EC6'
                    }}>{currentSchedule?.start_time.slice(0, -3)} - {currentSchedule?.end_time.slice(0, -3)}</span>
                </div>
            </Card>}

            <div style={{
                padding: '1.5rem 1.375rem',
                backgroundColor: '#FFFFFF',
                margin: '0 -1.375rem',
                borderRadius: '16px 16px 0 0',
                boxShadow: '0px -4px 40px rgba(0, 0, 0, 0.13)',
                flex: '1 1 0%'
            }}>
                <h4 style={{
                    fontSize: '1rem',
                    marginBottom: '0.5rem'
                }}>Layanan</h4>
                <p style={{
                    fontSize: '0.875rem',
                    color: '#A5A5A5',
                    marginBottom: '1.125rem'
                }}>Berikut adalah sesi waktu yang tersedia</p>

                {slots.length && slots.map(slot => {
                    return <Card key={slot.id} style={{
                        borderLeft: '12px solid #007EC6',
                        display: 'flex',
                        height: '80px',
                        alignItems: 'center',
                        marginBottom: '1rem',
                        opacity: slot.filled_slot === slot.max_slots ? '0.5' : '1'
                    }}>
                        <div style={{
                            flex: '1 1 0%'
                        }}>
                            <div style={{
                                fontWeight: '700'
                            }}>
                                {slot.start_time} - {slot.end_time}
                            </div>
                            <p style={{
                                fontSize: '0.75rem',
                                color: '#A5A5A5',
                                marginTop: '0.75rem',
                                display: 'flex',
                                alignItems: 'center'
                            }}>
                                <UserIcon
                                    color="#A5A5A5"
                                    height="1.25rem"
                                    style={{
                                        marginRight: '0.25rem'
                                    }}
                                />
                                <span style={{
                                    fontWeight: '700',
                                    color: '#007EC6'
                                }}>{slot.max_slots - slot.filled_slot}</span>/{slot.max_slots} Slot
                            </p>
                        </div>

                        <div style={{
                            display: 'inline-flex',
                            alignItems: 'center',
                            justifyContent: 'center'
                        }}>
                            <AngleRightIcon color="#103C7C" />
                        </div>
                    </Card>
                })}
            </div>
        </MainContent>
    </>
}

export default TimeSlotList