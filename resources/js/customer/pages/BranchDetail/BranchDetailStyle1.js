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
import BranchStatusOpen from '../../components/style1/BranchStatusOpenStyle1'
import BranchStatusClosed from '../../components/style1/BranchStatusClosedStyle1'
import MainContent from '../../components/MainContent'
import Card from '../../components/Card'
import BranchMap from '../../components/BranchMap'
import SliderIndicator from '../../components/SliderIndicator'
import IndustryCategoryWrapper from '../../components/style1/IndustryCategoryWrapper'
import BranchNameWrapper from '../../components/style1/BranchNameWrapper'

import ArrowLeftIcon from '../../icons/ArrowLeftIcon'
import ArrowDownIcon from '../../icons/ArrowDownIcon'
import SendIcon from '../../icons/SendIcon'
import PhoneIcon from '../../icons/PhoneIcon'
import LocationIcon from '../../icons/LocationIcon'
import useLocalization from '../../hooks/useLocalization'

const ScheduleItem = styled.div`
    display: flex;
    font-size: .875rem;
    margin-bottom: 1.125rem;

    &:last-child {
        margin-bottom: 0;
    }
`

export default function BranchDetail() {

    const {t, locale} = useLocalization();

    const PAGE_TITLE = t('Location Details')
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

    let isBranchOpen = false
    const [isOpen, setIsOpen] = useState(false)

    if (branchQuery.status === 'success') {
        branch = branchQuery.data
        coordinates = [branch.long, branch.lat]
        currentSchedule = branch.schedule.find(schedule => {
            return schedule.day === getDayName(new Date(), 'en')
        })
        isBranchOpen = currentSchedule.status === 'open'
    }

    let layer = ''
    if(branch && branch.branch_configuration.layer == 2) {
        layer = '/two-layer'
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
                        marginLeft: '8rem',
                        textTransform: 'capitalize',
                        flex: '1 1 0%'
                    }}>{PAGE_TITLE}</div>
                </Header>
            <Banner imageUrl={branch?.photo} style={{ borderRadius:'0px 0px 0px 0px' }}>
                            <SliderIndicator active={0} total={3} style={{
                                position: 'absolute',
                                bottom: '50%',
                                left: '50%',
                                transform: 'translateX(-50%)'
                            }} />
            </Banner>

            <MainContent style={{   position: 'absolute',
                                top: '25%',
                                width: '100%',
                                backgroundColor: 'white',
                                borderRadius: '20px 20px 0px 0px', }}>
                 <div style={{ display:'flex',alignItems:'center',margin:'1rem 2.5rem 2.5rem 2.5rem'}}>
                                <img src={branch?.logo ? `/storage/${branch?.logo}` : `/img/store.svg`}style={{ height:'4rem', marginRight:'2rem'}} />
                              <div>
                                <IndustryCategoryWrapper>
                                            {branch?.industry_category.name}
                                </IndustryCategoryWrapper>
                                <div>
                                    <BranchNameWrapper>
                                        {branch?.name}
                                    </BranchNameWrapper>
                                </div>
                                <div style={{ display:'flex',  alignItems: 'center', }}>
                                {!!currentSchedule && isBranchOpen
                                    ? <BranchStatusOpen
                                        startTime={currentSchedule.start_time.slice(0, 5)}
                                        endTime={currentSchedule.end_time.slice(0, 5)}
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
                                </div>
                        </div>
                    </div>
                    <hr style={{ marginBottom:'2rem' }}/>
                <Card style={{ marginBottom:'1.2rem' }}>
                    <div style={{
                        display: 'flex',
                        justifyContent: 'space-between',
                        alignItems: 'center'
                    }}>
                        <h4>{t('Operational Hours')}</h4>

                        <button style={{
                            display: 'flex',
                            alignItems: 'center',
                            backgroundColor: 'transparent',
                            border: 'none',
                            cursor: 'pointer'
                        }} onClick={() => setIsOpen(!isOpen)}>
                            <ArrowDownIcon color="#103C7C" style={{
                                transform: isOpen ? 'none' : 'rotate(-90deg)'
                            }} />
                        </button>
                    </div>

                    {isOpen && <div style={{
                        marginTop: '1.625rem'
                    }}>
                        {getDaysName(locale).map((day, idx) => {
                            const schedule = branch.schedule.find(schedule => {
                                return schedule.day === enDays[idx]
                            })

                            let listValue = ''

                            if (schedule?.status === 'closed') {
                                listValue = '-'
                            } else if (schedule?.status === 'fullday') {
                                listValue = t('Full day')
                            } else if (schedule?.status === 'open') {
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
                <Card>
                    <h4>{t('Contact')}</h4>
                    <div style={{ display:'flex', marginTop:'1.3rem', justifyContent:'space-between', gap:'1rem', flexWrap:'wrap' }}>
                        <div style={{ display:'flex', alignItems:'center' }}>
                            <div style={{ backgroundColor:'#103C7C',
                                        borderRadius:'6px',
                                        marginRight: '0.75rem',
                                        padding:'0.4rem 0.5rem' }}>
                                <span>
                                        <PhoneIcon color="#fff" width="22" height="22" />
                                </span>
                            </div>
                                <p style={{ color:'#8e8e8e', fontSize:'0.95rem',wordBreak:'break-word' }}>
                                    {branch.mobile_phone}
                                </p>
                        </div>
                        <div style={{ display:'flex', alignItems:'center' }}>
                            <div style={{ backgroundColor:'#103C7C',
                                        borderRadius:'6px',
                                        marginRight: '0.75rem',
                                        padding:'0.4rem 0.5rem' }}>
                                <span>
                                    <SendIcon color="#fff" width="22" height="22" />
                                </span>
                            </div>
                                <p style={{ color:'#8e8e8e', fontSize:'0.95rem',wordBreak:'break-word' }}>
                                    {branch.email}
                                </p>
                        </div>
                    </div>
                </Card>
                <Card style={{
                    marginTop: '1.625rem'
                }}>
                    <h4>{t('Location')}</h4>
                    <div>
                        {regencyQuery.status === 'success' && <div style={{
                            color: '#7A7A7A',
                            fontSize: '0.875rem',
                            display: 'flex',
                            alignItems: 'center',
                            marginTop:'0.7rem'
                        }}>
                            <div style={{ wordBreak:'break-word' }}>
                                {branch.address}
                                <p style={{
                                    marginTop: '0.125rem'
                                }}>{city.name}</p>
                            </div>
                        </div>}
                    </div>
                <div style={{
                    margin: '1.625rem 0'
                }}>
                    {!!coordinates.length && <BranchMap center={coordinates} marker={coordinates} />}
                </div>
                </Card>

            </MainContent>
        </>
    }
}
