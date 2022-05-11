import styled, { keyframes } from 'styled-components'

import CircleNotchIcon from '../icons/CircleNotchIcon'

const LoadingRoot = styled.div`
    background-color: rgba(0, 0, 0, 0.5);
    position: fixed;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
`

const spin = keyframes`
    0% {
        transform: rotate(0deg);
    }

    100% {
        transform: rotate(360deg);
    }
`

const LoadingIcon = styled(CircleNotchIcon)`
    fill: #FFFFFF;
    width: 2.5rem;
    height: 2.5rem;
    animation: ${spin} 1s linear infinite;
`

function Loading() {
    return <LoadingRoot>
        <LoadingIcon />
    </LoadingRoot>
}

export default Loading