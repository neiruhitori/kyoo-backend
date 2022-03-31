import { useState } from 'react'
import { Link, useParams } from 'react-router-dom'
import { useQuery } from 'react-query'
import styled from 'styled-components'

import { fetchBranch } from '../../api/branch'
import { getRegencyById } from '../../api/regency'
import { getDaysName, getDayName } from '../../utils/date'

import Header from '../../components/Header'
import Banner from '../../components/Banner'
import Chip from '../../components/Chip'
import H2 from '../../components/H2'
import BranchStatus from '../../components/BranchStatus'
import MainContent from '../../components/MainContent'
import Card from '../../components/Card'
import BranchMap from '../../components/BranchMap'

import ArrowLeftIcon from '../../icons/ArrowLeftIcon'
import ArrowDownIcon from '../../icons/ArrowDownIcon'
import SendIcon from '../../icons/SendIcon'
import PhoneIcon from '../../icons/PhoneIcon'
import LocationIcon from '../../icons/LocationIcon'

const ScheduleItem = styled.div`
    display: flex;
    font-size: .875rem;
    margin-bottom: 1.125rem;

    &:last-child {
        margin-bottom: 0;
    }
`

export default function BranchDetail() {
    const PAGE_TITLE = 'Detail Lokasi'
    const { branchId, queueType } = useParams()

    let branch = null
    let coordinates = []
    let currentSchedule = null
    let city = null
    const enDays = getDaysName('en')

    const branchQuery = useQuery('branch', () => fetchBranch(branchId))
    const regencyQuery = useQuery('regency', () => getRegencyById(branch?.regency_id), {
        enabled: branchQuery.status === 'success'
    })

    const [isOpen, setIsOpen] = useState(false)

    if (branchQuery.status === 'success') {
        branch = branchQuery.data
        coordinates = [branch.long, branch.lat]
        currentSchedule = branch.schedule.find(schedule => {
            return schedule.day === getDayName(new Date(), 'en')
        })
    }

    if (regencyQuery.status === 'success') {
        city = regencyQuery.data
    }

    if (branchQuery.status === 'loading') {
        return <div style={{
            padding: '2rem 1.375rem'
        }}>
            Loading...
        </div>
    }

    if (branchQuery.status === 'success') {
        return <>
            <Banner imageUrl="/img/queue.jpeg">
                <Header bgType="blur">
                    <div style={{
                        marginRight:' 0.75rem'
                    }}>
                        <Link to={`/kyooTicket/${queueType}/${branchId}/services`} style={{
                            display: 'flex',
                            justifyContent: 'center',
                            alignItems: 'center'
                        }}>
                            <ArrowLeftIcon color="#FFFFFF" />
                        </Link>
                    </div>

                    <div style={{ textTransform: 'capitalize' }}>{PAGE_TITLE}</div>
                </Header>

                <div style={{
                    padding: '1.625rem 1.375rem'
                }}>
                    <div style={{
                        marginBottom: '1rem'
                    }}>
                        <Chip label={branch.industry_category.name} />\
                    </div>

                    <div style={{
                        width: '224px'
                    }}>
                        <H2 style={{
                            color: '#FFFFFF'
                        }}>{branch.name}</H2>
                    </div>
                </div>
                
                <div style={{
                    marginTop: 'auto',
                    padding: '0 1.375rem'
                }}>
                    <BranchStatus
                        isOpen={currentSchedule.status === 'open'}
                        startTime={currentSchedule.start_time.slice(0, 5)}
                        endTime={currentSchedule.end_time.slice(0, 5)}
                        style={{
                            borderBottomLeftRadius: '0',
                            borderBottomRightRadius: '0',
                        }}
                    />
                </div>
            </Banner>

            <MainContent>
                <Card>
                    <div style={{
                        display: 'flex',
                        justifyContent: 'space-between',
                        alignItems: 'center'
                    }}>
                        <h4>Jam Operasional</h4>

                        <button style={{
                            display: 'flex',
                            alignItems: 'center',
                            backgroundColor: 'transparent',
                            border: 'none',
                            cursor: 'pointer'
                        }} onClick={() => setIsOpen(!isOpen)}>
                            <ArrowDownIcon color="#103C7C" style={{
                                transform: isOpen ? 'none' : 'rotate(180deg)'
                            }} />
                        </button>
                    </div>
                    
                    {isOpen && <div style={{
                        marginTop: '1.625rem'
                    }}>
                        {getDaysName().map((day, idx) => {
                            const schedule = branch.schedule.find(schedule => {
                                return schedule.day === enDays[idx]
                            })

                            let listValue = ''

                            if (schedule?.status === 'closed') {
                                listValue = '-'
                            } else if (schedule?.status === 'fullday') {
                                listValue = 'Sehari penuh'
                            } else {
                                listValue = `${schedule.start_time.slice(0, 5)} - ${schedule.end_time.slice(0, 5)}`
                            }

                            return <ScheduleItem key={day}>
                                <div style={{
                                    flex: '1',
                                    color: '#A5A5A5',
                                    textTransform: 'capitalize'
                                }}>{day}</div>
        
                                {schedule && <div style={{
                                    flex: '1',
                                    color: '#103C7C',
                                    fontWeight: '600',
                                    textAlign: 'right'
                                }}>
                                    {listValue}
                                </div>}
                            </ScheduleItem>
                        })}
                    </div>}
                </Card>

                <div style={{
                    marginTop: '1.625rem'
                }}>
                    <h4>Kontak</h4>
                    <div style={{
                        marginTop: '1.125rem'
                    }}>
                        <div style={{
                            marginBottom: '1.375rem',
                            color: '#7A7A7A',
                            fontSize: '0.875rem',
                            display: 'flex',
                            alignItems: 'center'
                        }}>
                            <span style={{
                                marginRight: '0.75rem'
                            }}>
                                <PhoneIcon color="#007EC6" width="22" height="22" />
                            </span>
                            {branch.mobile_phone}
                        </div>

                        <div style={{
                            marginBottom: '1.375rem',
                            color: '#7A7A7A',
                            fontSize: '0.875rem',
                            display: 'flex',
                            alignItems: 'center'
                        }}>
                            <span style={{
                                marginRight: '0.75rem'
                            }}>
                                <SendIcon color="#007EC6" width="22" height="22" />
                            </span>
                            {branch.email}
                        </div>

                        {regencyQuery.status === 'success' && <div style={{
                            color: '#7A7A7A',
                            fontSize: '0.875rem',
                            display: 'flex',
                            alignItems: 'center'
                        }}>
                            <span style={{
                                marginRight: '0.75rem'
                            }}>
                                <LocationIcon color="#007EC6" width="22" height="22" />
                            </span>
                            <div>
                                {branch.address}
                                <p style={{
                                    marginTop: '0.125rem'
                                }}>{city.name}</p>
                            </div>
                        </div>}
                    </div>
                </div>

                <div style={{
                    margin: '1.625rem 0'
                }}>
                    {!!coordinates.length && <BranchMap center={coordinates} marker={coordinates} />}
                </div>
            </MainContent>
        </>
    }
}