import { useParams, Link, useNavigate } from 'react-router-dom'
import { useQuery } from 'react-query'
import { getDayName, getFullDate } from '../../utils/date'
import { fetchBranch } from '../../api/branch'
import { fetchServiceByBranchId } from '../../api/services'
import useLocalization from '../../hooks/useLocalization'

import Header from '../../components/Header'
import Banner from '../../components/Banner'
import Chip from '../../components/Chip'
import H2 from '../../components/H2'
import BranchStatusOpen from '../../components/style1/BranchStatusOpenStyle1'
import BranchStatusClosed from '../../components/style1/BranchStatusClosedStyle1'
import SliderIndicator from '../../components/SliderIndicator'
import MainContent from '../../components/MainContent'
import ServiceItemSkeleton from '../../components/style1/ServiceItemSkeletonStyle1'
import ServiceItem from '../../components/style1/ServiceItemStyle1'
import ServiceCard from '../../components/style1/ServiceCardStyle1'
import Button from '../../components/style1/ButtonStyle1'
import DetailWrapper from '../../components/style1/DetailWrapper'
import BranchNameWrapper from '../../components/style1/BranchNameWrapper'
import IndustryCategoryWrapper from '../../components/style1/IndustryCategoryWrapper'
import DateSlider from '../../components/style1/DateSlider'
import SlotServiceComponent from './../../components/style1/SlotServiceComponent';

import AngleRightIcon from '../../icons/AngleRightIcon'

function ServiceList() {
    const {t, locale} = useLocalization();
    const { branchId, queueType } = useParams()
    const PAGE_TITLE = t(":queueType Queue", { queueType: queueType })
    const navigate = useNavigate()

    const selectedDate = new Date()

    const branchRes = useQuery('branch', () => fetchBranch(branchId))
    const servicesRes = useQuery('services',
        () => fetchServiceByBranchId(branchId, {
            queueType,
            date: getFullDate(selectedDate)
        })
    )

    let branch = null,
        schedule = null,
        services = [],
        isBranchOpen = false

    if (branchRes.status === 'success') {
        branch = branchRes.data
        schedule = branch?.schedule.find(v => (v.day === getDayName(new Date(), 'en')))
        isBranchOpen = branch?.is_today_open
    }

    if (servicesRes.status === 'success') {
        services = servicesRes.data
    }

    if(branch && branch.branch_type.is_direct_queue && branch.branch_configuration.layer === 2){
        navigate(`/customer/${branchId}/onsite/services/two-layer`);
    }

    return <>
        <Banner imageUrl={branch?.photo} style={{ borderRadius:'0px 0px 0px 0px' }}>
            
            <SliderIndicator active={0} total={3} style={{
                position: 'absolute',
                bottom: '40%',
                left: '50%',
                transform: 'translateX(-50%)'
            }} />
        </Banner>


        <MainContent style={{   position: 'absolute',
                                top: '25%',
                                width: '100%',
                                backgroundColor: 'white',
                                borderRadius: '20px 20px 0px 0px', }}>
            <div style={{ display:'flex', justifyContent: 'space-evenly',alignItems:'center',marginBottom:'2.5rem'  }}>
                            <img src={branch?.logo ? `/storage/${branch?.logo}` : `/img/store.svg`}style={{ height:'4rem'}} />
                          <div>
                            <IndustryCategoryWrapper>
                                        {branch?.industry_category.name}
                            </IndustryCategoryWrapper>
                            <div>
                                <BranchNameWrapper>
                                    {branch?.name}
                                </BranchNameWrapper>
                            </div>
                            <div style={{ display:'flex', justifyContent: 'space-between',  alignItems: 'center', }}>
                            {isBranchOpen()
                                ? <BranchStatusOpen
                                    startTime={todaySchedule?.start_time.slice(0, -3)}
                                    endTime={todaySchedule?.end_time.slice(0, -3)}
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
                            <Link to={`/customer/${branch?.id}/appointment/detail`} style={{ alignContent:'end' }}>
                                <DetailWrapper>
                                    <span style={{padding: '0 0.75rem'}}>{t('See Details')}</span>
                                    <AngleRightIcon color="#33A0FF" style={{
                                        display:'block',
                                        margin:'auto',
                                        borderRadius:'50%',
                                        backgroundColor:'#fff',
                                        fill:'#33A0FF',
                                        padding:'3px'
                                    }}/>
                                </DetailWrapper>
                            </Link>
                            </div>
                          </div>
                        </div>
                        <hr style={{ marginBottom:'2rem' }}/>


            <h4 style={{
                fontSize: '1rem',
                marginBottom: '1.125rem'
            }}>{t('Service')}</h4>

            {servicesRes.status === 'loading' && <ServiceItemSkeleton />}

            {schedule && isBranchOpen && servicesRes.status === 'success' && services.map(service => {
                if (!service.is_show) return;

                const serviceProps = {
                    title: service.name,
                    key: service.id,
                    action: {
                        label: t('Total Queue'),
                        value: service.total_queue
                    }
                }

                return <Link to={`${service.id}/visitor`} key={service.id} style={{
                    marginBottom: '1.125rem'
                }}>
                    <ServiceItem {...serviceProps} />
                </Link>
            })}
        </MainContent>
    </>
}

export default ServiceList
