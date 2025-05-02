import styled from 'styled-components'

import Card from '../Card'
import BlueCard from '../BlueCard'
import AngleRightIcon from '../../icons/AngleRightIcon'
import ArrowDownIcon from '../../icons/ArrowDownIcon';

const ServiceContent = styled.div(() => ({
    flex: '1 1 0%'
}))

const ServiceTitle = styled.div(() => ({
    fontWeight: '700',
    color: 'white',
    fontSize: '1.25rem'
}))

const ServiceSubtitle = styled.div(() => ({
    fontSize: '0.75rem',
    backgroundColor: '#4a90e2',
    borderRadius: '15px',
    padding: '1px 10px',
    color: 'white',
    marginTop: '0.75rem'
}))

const ServiceAction = styled.div(() => ({
    boxShadow: 'none',
    width: '125px',
    display: 'flex'
}))

const ServiceLabel = styled.p(() => ({
    color:'white',
    fontSize: '0.75rem',
    marginBottom: '0.75rem',
    fontWeight: '500'
}))

const TimeLabel = styled.div(() => ({
    fontSize: '0.75rem',
    padding: '1px 10px',
    color: 'white',
    marginTop: '0.75rem'
}))

const ServiceLabelValue = styled.div`
    span {
        color: #D6D6D6;
        font-size: 1.5rem;
    }

    span:first-child {
        font-size: 1.5rem;
        color: #FFFFFF;
    }
`

const ServiceActionIcon = styled.div(() => ({
    position: 'relative',
    width: '1.125rem'
}))

function ServiceItem(props) {
    return <Card {...props} style={{
        display: 'flex',
        height: '85px',
        boxShadow: '0px 7px 40px rgba(0, 0, 0, 0.1)',
        borderRadius:  props.isSelected ? '12px 12px 0px 0px' : '12px',
        backgroundImage: 'linear-gradient(270deg, #103C7C -24.91%, #2F5B9B 118.64%)',
        padding: '1.125rem',
        cursor: 'pointer',
    }}>
        <ServiceContent>
            <ServiceTitle>{props.title}</ServiceTitle>
            <div style={{ display:'flex' }}>
            {props.subtitle && <ServiceSubtitle>{props.subtitle}</ServiceSubtitle>}
            {props.timeSlot && <TimeLabel>{props.timeSlot}</TimeLabel>}
            </div>
        </ServiceContent>
        <div style={{ borderLeft:'2px solid white', height:'50px' , marginRight:'15px' }}></div>

        {props.action && <ServiceAction>
            <div style={{
                flex: '1 1 0%'
            }}>
                <ServiceLabel>
                    {props.action.label}
                </ServiceLabel>

                <ServiceLabelValue>
                    <span>{props.action.value}/</span>
                    {typeof props.action.total !== 'undefined' && <span>{props.action.total}</span>}
                </ServiceLabelValue>
            </div>

            <ServiceActionIcon>
                {props.isSelected ? 
                <ArrowDownIcon color="#FFFFFF" style={{
                    position: 'absolute',
                    top: '50%',
                    transform: 'translateY(-50%)',
                    right: '0'
                }}/>
                :
                <AngleRightIcon color="#FFFFFF" style={{
                    position: 'absolute',
                    top: '50%',
                    transform: 'translateY(-50%)',
                    right: '0'
                }} />
                }
            </ServiceActionIcon>
        </ServiceAction>}
    </Card>
}

export default ServiceItem