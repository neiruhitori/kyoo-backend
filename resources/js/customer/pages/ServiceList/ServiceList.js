import { useParams, Link } from 'react-router-dom'
import { useQuery } from 'react-query'
import { getDayName, getFullDate } from '../../utils/date'
import { fetchBranch } from '../../api/branch'
import { fetchServiceByBranchId } from '../../api/services'

import Header from '../../components/Header'
import Banner from '../../components/Banner'
import Chip from '../../components/Chip'
import H2 from '../../components/H2'
import BranchStatusOpen from '../../components/BranchStatusOpen'
import BranchStatusClosed from '../../components/BranchStatusClosed'
import SliderIndicator from '../../components/SliderIndicator'
import ServiceItem from '../../components/ServiceItem'
import MainContent from '../../components/MainContent'
import ServiceItemSkeleton from '../../components/ServiceItemSkeleton'

import AngleRightIcon from '../../icons/AngleRightIcon'

function ServiceList() {
    const { branchId, queueType } = useParams()
    const PAGE_TITLE = `Antrian ${queueType}`

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

                {schedule && isBranchOpen
                ? <BranchStatusOpen
                    startTime={schedule.start_time.slice(0, -3)}
                    endTime={schedule.end_time.slice(0, -3)}
                    style={{
                        marginTop: '1rem'
                    }}
                />
                : <BranchStatusClosed
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
            <h4 style={{
                fontSize: '1rem',
                marginBottom: '1.125rem'
            }}>Layanan</h4>

            {servicesRes.status === 'loading' && <ServiceItemSkeleton />}

            {isBranchOpen && servicesRes.status === 'success' && services.map(service => {
                if (!service.is_show) return;

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
            })}
        </MainContent>
    </>
}

export default ServiceList