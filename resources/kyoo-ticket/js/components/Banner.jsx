import styled from 'styled-components'

import AngleRightIcon from '../icons/AngleRightIcon'
import BulletIcon from '../icons/BulletIcon'
import Chip from './Chip'

const backgroundImage = '/img/queue.jpeg'

const BannerWrapper = styled.div`
    padding: 2rem 1.75rem 3.25rem 1.75rem;
    background: linear-gradient(180deg, rgba(0, 0, 0, 0.21) 0%, #000000 142.65%), url('${backgroundImage}'), #C4C4C4;
    background-size: cover;
    border-radius: 0 0 16px 16px;
    min-height: 216px;
    position: relative;
`

function getDay(id) {
    const days = [
        'sunday', 'monday', 'tuesday',
        'wednesday', 'thursday', 'friday',
        'saturday'
    ]

    return days[id]
}

function Banner(props) {
    const currentDay = getDay(new Date().getDay())
    const currentSchedule = props.branch?.schedule.find(v => (v.day === currentDay))

    return <BannerWrapper>
        <div style={{
            display: 'flex',
            alignItems: 'center',
            justifyContent: 'space-between',
            marginBottom: '1rem'
        }}>
            {/* Chip: Queue type */}
            <Chip label={props.branch?.industry_category.name} />

            {/* DetaiLink: Location detail */}
            <a href="#">
                <div style={{
                    color: '#FFFFFF',
                    display: 'flex',
                    alignItems: 'center'
                }}>
                    <span style={{
                        fontSize: '0.75rem',
                        padding: '0 0.75rem'
                    }}>Lihat Detail</span>

                    <AngleRightIcon color="#FFFFFF" />
                </div>
            </a>
        </div>

        <div style={{
            marginBottom: '1rem',
            width: '224px'
        }}>
            {/* H2: Branch name */}
            <h2 style={{
                fontWeight: '700',
                fontSize: '1.75rem',
                color: '#FFFFFF'
            }}>{props.branch?.name}</h2>
        </div>
        
        {/* WorkTimeInfo: Status and work time */}
        {currentSchedule && <div style={{
            display: 'inline-flex',
            alignItems: 'center',
            justifyContent: 'center',
            backgroundColor: 'lightgray',
            borderRadius: '6px',
            fontSize: '0.875rem',
            height: '2.125rem',
            color: '#FFFF',
            background: 'rgba(255, 255, 255, 0.1)',
            boxShadow: '16px -4px 40px rgba(0, 0, 0, 0.05)',
            backdropFilter: 'blur(16px)'
        }}>
            <span style={{
                paddingLeft: '0.625rem',
                paddingRight: '0.375rem',
                display: 'inline-flex',
                alignItems: 'center',
                color: currentSchedule.status == 'open' ? '#3EAF3F' : '#D21C1C'
            }}>
                <BulletIcon color={currentSchedule.status == 'open' ? '#3EAF3F' : '#D21C1C'} style={{
                    width: '0.5rem',
                    height: '0.5rem',
                    marginRight: '0.375rem'
                }} />
                {currentSchedule.status == 'open' ? 'BUKA' : 'TUTUP'}
            </span>|
            <span style={{
                paddingRight: '0.625rem',
                paddingLeft: '0.375rem'
            }}>
                {currentSchedule.start_time.slice(0, -3)} - {currentSchedule.end_time.slice(0, -3)}
            </span>
        </div>}
        
        {/* SliderStatus: show current slide */}
        <div style={{
            display: 'flex',
            alignItems: 'center',
            justifyContent: 'center',
            position: 'absolute',
            left: '50%',
            transform: 'translateX(-50%)',
            bottom: '0.875rem'
        }}>
            <span style={{
                marginRight: '0.375rem'
            }}>
                <BulletIcon color="#FFFFFF" style={{
                    width: '0.5rem',
                    height: '0.5rem'
                }} />
            </span>

            <span style={{
                marginRight: '0.375rem'
            }}>
                <BulletIcon color="#FFFFFF" style={{
                    width: '0.5rem',
                    height: '0.5rem',
                    opacity: '0.5'
                }} />
            </span>

            <span>
                <BulletIcon color="#FFFFFF" style={{
                    width: '0.5rem',
                    height: '0.5rem',
                    opacity: '0.5'
                }} />
            </span>
        </div>
    </BannerWrapper>
}

export default Banner