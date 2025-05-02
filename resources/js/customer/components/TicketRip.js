import styled from 'styled-components'

const TicketRipRoot = styled.div`
    height: 32px;
    margin: 0 16px;
    background-color: white;
    position: relative;
    display: flex;
    align-items: center;

    &:before {
        content: '';
        position: absolute;
        width: 32px;
        height: 32px;
        top: 50%;
        left: -16px;
        transform: translate(-50%, -50%) rotate(45deg);
        border: 8px solid transparent;
        border-top-color: white;
        border-right-color: white;
        border-radius: 100%;
    }

    &:after {
        content: '';
        position: absolute;
        width: 32px;
        height: 32px;
        top: 50%;
        right: -63px;
        transform: translate(-50%, -50%) rotate(225deg);
        border: 8px solid transparent;
        border-top-color: white;
        border-right-color: white;
        border-radius: 100%;
    }
`

export default function TicketRip() {
    return <TicketRipRoot>
        <div style={{
            borderTop: '2px dashed #90B7F1',
            width: '100%'
        }}></div>
    </TicketRipRoot>
}