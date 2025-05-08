import { useState } from 'react'
import { Link, useParams } from 'react-router-dom'
import { useQuery } from 'react-query'
import styled from 'styled-components'

import { fetchBranch } from '../../api/branch'
import { getRegencyById } from '../../api/regency'
import { getDaysName, getDayName } from '../../utils/date'

import Header from '../../components/Header'
import Banner from '../../components/style3/Banner'
import Chip from '../../components/Chip'
import H2 from '../../components/H2'
import BranchStatusOpen from '../../components/style1/BranchStatusOpenStyle1'
import BranchStatusClosed from '../../components/style1/BranchStatusClosedStyle1'
import MainContent from '../../components/MainContent'
import Card from '../../components/Card'
import BranchMap from '../../components/BranchMap'
import SliderIndicator from '../../components/SliderIndicator'
import DetailIndustryCategory from '../../components/style3/DetailIndustryCategory'
import BranchNameWrapper from '../../components/style1/BranchNameWrapper'
import DetailIconWrapper from '../../components/style3/DetailIcon'

import ArrowLeftIcon from '../../icons/ArrowLeftIcon'
import ArrowDownIcon from '../../icons/ArrowDownIcon'
import SendIcon from '../../icons/SendIcon'
import PhoneIcon from '../../icons/PhoneIcon'
import LocationIcon from '../../icons/LocationIcon'
import useLocalization from '../../hooks/useLocalization'
import BoxOpenIcon from './../../icons/BoxOpenIcon';
import CalendarIcon from '../../icons/CalendarIcon'
import ClockIcon from '../../icons/ClockIcon'
import ModalBackdrop from '../../components/style3/ModalStyle3'
import ModalContent from '../../components/style3/ModalContentStyle3'

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
    const [isModalOpen, setIsModalOpen] = useState(false)
    const [activeTab, setActiveTab] = useState(null)

    const handleOpenModal = (tab) => {
        setActiveTab(tab);
        setIsModalOpen(true);
      };

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
            padding: '2rem 1.375rem',
            color:'white'
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
                <div style={{ display:'flex',alignItems:'center', flexDirection:'column' }}>
                    <img src={branch?.logo ? `/storage/${branch?.logo}` : `/img/store.svg`}
                        style={{ height:'7rem', borderRadius:'100%', marginTop:'2.5rem', marginBottom:'1rem'}} />
                    <DetailIndustryCategory>
                        {branch?.industry_category.name}
                    </DetailIndustryCategory>
                    <BranchNameWrapper style={{ color:'white' }}>
                        {branch?.name}
                    </BranchNameWrapper>
                    <div style={{ display:'flex',color:'white', marginBottom:'1.5rem' }}>
                    {!!currentSchedule && isBranchOpen
                                        ? <BranchStatusOpen
                                            startTime={currentSchedule.start_time.slice(0, 5)}
                                            endTime={currentSchedule.end_time.slice(0, 5)}
                                            style={{
                                                marginTop: '1rem',
                                                
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
                    <div style={{ display:'flex', gap:'2rem' }}>
                        <DetailIconWrapper label={t('Phone')} onClick={() => handleOpenModal('phone') }>
                                <PhoneIcon height="25" />
                        </DetailIconWrapper>
                        <DetailIconWrapper label='Email' onClick={() => handleOpenModal('email') }>
                                <SendIcon height="25" />
                        </DetailIconWrapper>
                        <DetailIconWrapper label={t('Open Hours')} onClick={() => handleOpenModal('operational') }>
                                <CalendarIcon height="25" />
                        </DetailIconWrapper>
                    </div>

                </div>

                {isModalOpen && (
                    <ModalBackdrop onClick={() => setIsModalOpen(false)}>
                        <ModalContent onClick={(e) => e.stopPropagation()}
                                style={{ minHeight: activeTab == 'operational' ? '40vh' : '30vh' }}>
                           {activeTab == 'phone' &&(
                            <>
                            <h4 style={{ textAlign:'center', marginBottom:'2rem' }}>
                                {t('Phone')}
                            </h4>
                            <div style={{ display: 'flex', gap:'1.2rem', alignItems:'center' }}>
                            <PhoneIcon color='white' height="40"
                                        style={{ backgroundColor: '#092044',
                                                borderRadius: '5px',
                                                padding: '0.5rem',
                                                display: 'flex',
                                                alignItems: 'center',
                                                justifyContent: 'center', }}/>
                                    <p>{branch?.mobile_phone}</p>
                            </div>
                            </>)}
                           {activeTab == 'email' &&(
                            <>
                            <h4 style={{ textAlign:'center', marginBottom:'2rem' }}>
                                Email
                            </h4>
                            <div style={{ display: 'flex', gap:'1.2rem', alignItems:'center' }}>
                            <SendIcon color='white' height="40"
                                        style={{ backgroundColor: '#092044',
                                                borderRadius: '5px',
                                                padding: '0.5rem',
                                                display: 'flex',
                                                alignItems: 'center',
                                                justifyContent: 'center', }}/>
                                    <p>{branch?.email}</p>
                            </div>
                            </>)}
                           {activeTab == 'operational' &&(
                            <>
                            <h4 style={{ textAlign:'center' }}>{t('Open Hours')}</h4>
                            <div style={{ marginTop: '1.625rem' }}>
                            {getDaysName(locale).map((day, idx) => {
                                const schedule = branch.schedule.find(schedule => {
                                   return schedule.day === enDays[idx]})
                                        let listValue = ''
                                    if (schedule?.status === 'closed') {
                                        listValue = '-'
                                    } else if (schedule?.status === 'fullday') {
                                        listValue = t('Full day')
                                    } else if (schedule?.status === 'open') {
                                        listValue = `${schedule.start_time.slice(0, 5)} - ${schedule.end_time.slice(0, 5)}`
                                    }
                           
                                    return <ScheduleItem key={day}>
                                        <div style={{ flex: '1',
                                                    color: '#A5A5A5',
                                                    textTransform: 'capitalize'
                                            }}>{day}</div>
                           
                                        {schedule && <div style={{
                                                        flex: '1',
                                                        color: '#103C7C',
                                                        fontWeight: '600',
                                                        textAlign: 'right'}}>
                                                            {listValue}
                                                    </div>}
                                        </ScheduleItem>})}
                            </div>
                            </>)}
                        </ModalContent>
                    </ModalBackdrop>
                )}
            </Banner>
            

            <MainContent style={{   position: 'absolute',
                                top: '55%',
                                width: '100%',
                                backgroundColor: 'white',
                                borderRadius: '20px 20px 0px 0px', }}>
            
            

                <Card>
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
