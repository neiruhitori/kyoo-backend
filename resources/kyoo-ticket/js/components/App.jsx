import { useState, useEffect } from 'react';
import { useParams } from 'react-router-dom'
import styled from 'styled-components'
import http from '../utils/http'

import DayPicker from 'react-day-picker';
import Header from './Header'
import Banner from './Banner'
import TextField from './TextField'

import ClockIcon from '../icons/ClockIcon'
import AngleRightIcon from '../icons/AngleRightIcon'
import CalendarIcon from '../icons/CalendarIcon'

import 'react-day-picker/lib/style.css'

const BRANCH_ID = window.location.href.split('/').pop()

const Wrapper = styled.div`
    max-width: 460px;
    margin: 0 auto;
    background-color: #FFFFFF;
    position: relative;
    min-height: 100vh;
`

function formatDate(date) {
    const months = [
        'Januari', 'Februari', 'Maret',
        'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September',
        'Oktober', 'November', 'Desember'
    ]

    return date.getDate() + ' ' + months[date.getMonth()] +  ' ' + date.getFullYear()
}

function fetchBranch(id) {
    return http.get(`branch/${id}`)
        .then(res => (res.data?.data))
}

function fetchServices(id) {
    return http.get(`service/branch/${id}`)
        .then(res => (res.data?.data))
}

function fetchServiceSlots(id, date) {
    return http.post('slot', {
        service_id: id,
        date
    }).then(res => (res.data?.data))
}

function App() {
    const [branch, setBranch] = useState(null)
    const [services, setServices] = useState([])
    const [selectedDate, setSelectedDate] = useState(new Date())
    const [showCalendar, setShowCalendar] = useState(false)

    function handleDayClick(day, modifiers = {}) {
        if (modifiers.disabled) {
            return
        }
        
        setSelectedDate(day)
    }

    function getClosedDays() {
        const days = {
            'sunday': 0,
            'monday': 1,
            'tuesday': 2,
            'wednesday': 3,
            'thursday': 4,
            'friday': 5,
            'saturday': 6
        }
        return branch ? branch.schedule.filter(v => {
            return v.status === 'closed'
        }).map(val => {
            return days[val.day]
        }) : []
    }

    useEffect(async () => {
        const newBranch = await fetchBranch(BRANCH_ID)
        setBranch(newBranch)

        const newServices = await fetchServices(BRANCH_ID)
        setServices(newServices)

        const serviceWithSlot = await Promise.all(newServices.map(async service => {
            service.slots = await fetchServiceSlots(service.id, new Date())

            return service
        }))
        setServices(serviceWithSlot)
    }, [])

    useEffect(async () => {
        const serviceWithSlot = await Promise.all(services.map(async service => {
            service.slots = await fetchServiceSlots(service.id, selectedDate)

            return service
        }))
        setServices(serviceWithSlot)
    }, [selectedDate])

    return <Wrapper>
        <Header />

        <Banner branch={branch} />

        <div style={{
            padding: '2rem 1.375rem'
        }}>
            {showCalendar && <div
                onClick={() => setShowCalendar(false)}
                style={{
                    backgroundColor: 'rgba(0, 0, 0, 0.5)',
                    position: 'fixed',
                    top: 0,
                    right: 0,
                    bottom: 0,
                    left: 0,
                    display: 'flex',
                    justifyContent: 'center',
                    alignItems: 'center',
                    zIndex: '9999'
                }}
            >
                <div
                    onClick={e => e.stopPropagation()}
                    style={{
                        backgroundColor: '#FFFFFF',
                        borderRadius: '12px'
                    }}
                >
                    <style>
                        {`
                            .DayPicker-Day--today {
                                background-color: #E8F4FD;
                                color: #005AA3;
                            }
                        `}
                    </style>

                    <DayPicker
                        onDayClick={handleDayClick}
                        months={[
                            'Januari', 'Februari', 'Maret',
                            'April', 'Mei', 'Juni',
                            'Juli', 'Agustus', 'September',
                            'Oktober', 'November', 'Desember'
                        ]}
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
                </div>
            </div>}

            <TextField
                label="Tanggal"
                style={{
                    marginBottom: '1.5rem'
                }}
                value={formatDate(selectedDate)}
                readOnly
                endAdornment={
                    <button
                        onClick={() => setShowCalendar(true)}
                        style={{
                            display: 'flex',
                            justifyContent: 'center',
                            alignItems: 'center',
                            padding: '0 1rem',
                            background: 'transparent',
                            border: 'none',
                            cursor: 'pointer'
                        }}
                    >
                        <CalendarIcon style={{
                            height: '24',
                            width: '24'
                        }} />
                    </button>
                }
            />

            <h4 style={{
                fontSize: '1rem',
                marginBottom: '1.125rem'
            }}>Layanan</h4>

            {services.map(service => {
                const totalSlot = service.slots ? service.slots.reduce((acc, curr) => {
                    return acc + curr.max_slots
                }, 0) : 0
                const totalFilledSlot = service.slots ? service.slots.reduce((acc, curr) => {
                    return acc + curr.filledSlot
                }, 0) : 0

                return <div style={{
                    boxShadow: '0px 7px 40px rgba(0, 0, 0, 0.1)',
                    borderRadius: '12px',
                    display: 'flex',
                    marginBottom: '1.125rem'
                }}>
                    <div style={{
                        padding: '1.125rem',
                        flex: '1 1 0%'
                    }}>
                        <div style={{
                            fontWeight: '700',
                            fontSize: '1rem',
                            marginBottom: '0.75rem'
                        }}>{service.name}</div>
    
                        <p style={{
                            fontSize: '0.75rem',
                            color: '#A5A5A5',
                            display: 'flex',
                            alignItems: 'center'
                        }}>
                            <ClockIcon color="#A5A5A5" style={{
                                width: '0.75rem',
                                height: '0.75rem',
                                marginRight: '0.5rem'
                            }} />
                            <span>{service.slots?.length} Sesi Waktu</span>
                        </p>
                    </div>
    
                    <div style={{
                        width: '164px',
                        background: 'linear-gradient(270deg, #103C7C -24.91%, #2F5B9B 118.64%)',
                        color: '#FFFF',
                        padding: '1.125rem',
                        borderRadius: '12px',
                        display: 'flex'
                    }}>
                        <div style={{
                            flex: '1 1 0%'
                        }}>
                            <p style={{
                                fontSize: '0.75rem',
                                marginBottom: '0.75rem',
                                fontWeight: '500'
                            }}>
                                Total Slot Tersedia
                            </p>
                            <div>
                                <span style={{
                                    fontSize: '1.75rem',
                                    fontWeight: '700'
                                }}>{totalSlot - totalFilledSlot}</span>

                                <span style={{
                                    color: '#D6D6D6',
                                    fontSize: '1rem'
                                }}>/{totalSlot}</span>
                            </div>
                        </div>

                        <div style={{
                            position: 'relative',
                            width: '1.125rem'
                        }}>
                            <AngleRightIcon color="#FFFFFF" style={{
                                position: 'absolute',
                                top: '50%',
                                transform: 'translateY(-50%)',
                                right: '0'
                            }} />
                        </div>
                    </div>
                </div>
            })}
        </div>
    </Wrapper>
}

export default App