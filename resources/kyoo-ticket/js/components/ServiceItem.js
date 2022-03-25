import styled from 'styled-components'

import AngleRightIcon from '../icons/AngleRightIcon'

const ServiceRoot = styled.div`
    box-shadow: 0px 7px 40px rgba(0, 0, 0, 0.1);
    border-radius: 12px;
    display: flex;
    background-color: #FFFFFF;
`

const ServiceContent = styled.div(() => ({
    padding: '1.125rem',
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

const ServiceAction = styled.div(() => ({
    width: '164px',
    background: 'linear-gradient(270deg, #103C7C -24.91%, #2F5B9B 118.64%)',
    color: '#FFFF',
    padding: '1.125rem',
    borderRadius: '12px',
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
    return <ServiceRoot>
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
    </ServiceRoot>
}

export default ServiceItem