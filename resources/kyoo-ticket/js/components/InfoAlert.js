import styled from 'styled-components'

import CircleInfoIcon from '../icons/CircleInfoIcon'

const InfoAlertRoot = styled.div`
    background-color: #E8F4FD;
    color: #005AA3;
    box-sizing: 6px;
    border: .5px solid #D0E9FB;
    display: flex;
    border-radius: 6px;
    padding: 1.125rem;
`

export default function InfoAlert(props) {
    return <InfoAlertRoot style={props.style}>
        <span style={{
            display: 'inline-block'
        }}>
            <CircleInfoIcon color="#005AA3" />
        </span>

        <div style={{
            flex: '1 1 0%',
            paddingLeft: '1rem',
            fontSize: '.875rem'
        }}>
            {props.children}
        </div>
    </InfoAlertRoot>
}