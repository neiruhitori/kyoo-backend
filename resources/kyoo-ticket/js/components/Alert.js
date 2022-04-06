import styled from 'styled-components';

import CircleExclamationIcon from '../icons/CircleExclamationIcon'

const AlertRoot = styled.div`
    padding: 1.125rem;
    background-color: rgb(22, 11, 11);
    border-radius: 6px;
    color: rgb(244, 199, 199);
    display: flex;
    line-height: 1.5;
`

function Alert({ children, style }) {
    return <AlertRoot style={style}>
        <span style={{
            marginRight: '1rem',
            display: 'inline-block'
        }}>
            <CircleExclamationIcon color="rgb(244, 67, 54)" />
        </span>
        <div style={{
            flex: '1 1 0%'
        }}>
            {children}
        </div>
    </AlertRoot>
}

export default Alert