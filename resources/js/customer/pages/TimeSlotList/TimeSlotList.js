import { Link, useParams, useSearchParams } from 'react-router-dom'
import { useState } from 'react'
import { format, formatBrowser, getMonthNames, getDayIndex, getDayName, getFullDate } from '../../utils/date'
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
import ServiceCardSkeleton from '../../components/ServiceCardSkeleton'

import ArrowLeftIcon from '../../icons/ArrowLeftIcon'
import CalendarIcon from '../../icons/CalendarIcon'
import ClockIcon from '../../icons/ClockIcon'
import AngleRightIcon from '../../icons/AngleRightIcon'
import UserIcon from '../../icons/UserIcon'
import BoxOpenIcon from '../../icons/BoxOpenIcon'
import SkeletonItem from '../../components/SkeletonItem'
import ProgressStep from '../../components/ProgressStep'

function TimeSlotList() {
    const PAGE_TITLE = 'Slot Waktu'
    const { branchId, serviceId, queueType } = useParams()
    const [searchParams] = useSearchParams()
    const date = searchParams.get('date') ? formatBrowser(searchParams.get('date')) : new Date()

    const [showCalendar, setShowCalendar] = useState(false)
    const [selectedDate, setSelectedDate] = useState(date)

    const branchRes = useQuery('branch', () => fetchBranch(branchId))
    const serviceRes = useQuery(['service', selectedDate], () => fetchServiceById(serviceId, {
        queueType,
        date: getFullDate(selectedDate)
    }))

    let branch = null
    let service = null
    let slots = []
    let schedule = null

    if (branchRes.status === 'success') {
        branch = branchRes.data
        schedule = branch?.schedule.find(v => (v.day === getDayName(selectedDate, 'en')))
    }

    if (serviceRes.status === 'success') {
        service = serviceRes.data
        slots = service.slot.filter(v => {
            return v.day === getDayName(selectedDate, 'en')
        })
    }

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

    return <>
        <Header>
            <div style={{
                height: '3.2rem',
                display: 'flex'
            }}>
                <Link to={-1} style={{
                    display: 'flex',
                    justifyContent: 'center',
                    alignItems: 'center',
                    padding: '.85rem 1.375rem'
                }}>
                    <ArrowLeftIcon />
                </Link>
            </div>

            <div style={{
                textTransform: 'capitalize',
                flex: '1 1 0%'
            }}>{PAGE_TITLE}</div>

            <div style={{
                width: '100px',
                padding: '0 1.375rem'
            }}>
                <ProgressStep active="0" total="3" />
            </div>
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

            {serviceRes.status === 'loading' && <ServiceCardSkeleton style={{
                marginBottom: '1.5rem'
            }} />}

            {serviceRes.status === 'success' && <Card style={{
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
                    <span>
                        {
                            slots.length
                                ? slots.length + ' Sesi Waktu Tersedia'
                                : 'Tidak Ada Sesi Waktu Tersedia'
                        }
                    </span>
                    <span style={{
                        margin: '0 0.5rem'
                    }}>|</span>
                    <span style={{
                        fontWeight: '700',
                        color: '#007EC6'
                    }}>{schedule?.start_time.slice(0, -3)} - {schedule?.end_time.slice(0, -3)}</span>
                </div>
            </Card>}

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
                    }}>Layanan</h4>
                    <p style={{
                        fontSize: '0.875rem',
                        color: '#A5A5A5'
                    }}>Berikut adalah sesi waktu yang tersedia</p>
                </div>

                {serviceRes.status === 'loading' && <Card style={{
                    borderLeft: '12px solid #007EC6',
                    height: '80px'
                }}>
                    <SkeletonItem height="1rem" width="100px" />
                    <SkeletonItem height=".75rem" style={{
                        marginTop: '1rem'
                    }} />
                </Card>}

                {serviceRes.status === 'success' && !slots.length && <div style={{
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
                    <h4>Sesi waktu kosong</h4>
                    <p style={{
                        textAlign: 'center',
                        width: '280px',
                        marginTop: '0.5rem',
                        color: '#A5A5A5',
                        fontSize: '.875rem'
                    }}>
                        Pilih tanggal lain untuk menemukan sesi waktu yang tersedia
                    </p>
                </div>}

                {serviceRes.status === 'success' && slots.map(slot => {
                    const isFull = slot.filled_slot === slot.max_slots

                    return <Link to={!isFull
                        ? `visitor?date=${getFullDate(selectedDate)}&slot=${slot.id}`
                        : '#'
                    } key={slot.id} style={{
                        marginBottom: '1rem'
                    }}>
                        <Card style={{
                            borderLeft: '12px solid #007EC6',
                            display: 'flex',
                            height: '80px',
                            alignItems: 'center',
                            opacity: isFull ? '0.5' : '1'
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
                    </Link>
                })}
            </div>
        </MainContent>
    </>
}

export default TimeSlotList