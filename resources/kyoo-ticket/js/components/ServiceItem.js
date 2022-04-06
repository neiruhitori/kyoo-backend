import styled from 'styled-components'

import Card from './Card'
import BlueCard from './BlueCard'
import AngleRightIcon from '../icons/AngleRightIcon'

const ServiceContent = styled.div(() => ({
    flex: '1 1 0%'
}))

const ServiceTitle = styled.div(() => ({
    fontWeight: '700',
    fontSize: '1rem'
}))

const ServiceSubtitle = styled.div(() => ({
    fontSize: '0.75rem',
    color: '#A5A5A5',
    marginTop: '0.75rem'
}))

const ServiceAction = styled(BlueCard)(() => ({
    width: '164px',
    margin: '-1.125rem -1.125rem -1.125rem 0',
    display: 'flex'
}))

const ServiceLabel = styled.p(() => ({
    fontSize: '0.75rem',
    marginBottom: '0.75rem',
    fontWeight: '500'
}))

const ServiceLabelValue = styled.div`
    span {
        color: #D6D6D6;
        font-size: 1rem;
    }

    span:first-child {
        font-size: 1.75rem;
        font-weight: 700;
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
        ...props.style,
        cursor: 'pointer'
    }}>
        <ServiceContent>
            <ServiceTitle>{props.title}</ServiceTitle>

            {props.subtitle && <ServiceSubtitle>{props.subtitle}</ServiceSubtitle>}
        </ServiceContent>

        {props.action && <ServiceAction>
            <div style={{
                flex: '1 1 0%'
            }}>
                <ServiceLabel>
                    {props.action.label}
                </ServiceLabel>

                <ServiceLabelValue>
                    <span>{props.action.value}</span>
                    {typeof props.action.total !== 'undefined' && <span>/{props.action.total}</span>}
                </ServiceLabelValue>
            </div>

            <ServiceActionIcon>
                <AngleRightIcon color="#FFFFFF" style={{
                    position: 'absolute',
                    top: '50%',
                    transform: 'translateY(-50%)',
                    right: '0'
                }} />
            </ServiceActionIcon>
        </ServiceAction>}
    </Card>
}

export default ServiceItem