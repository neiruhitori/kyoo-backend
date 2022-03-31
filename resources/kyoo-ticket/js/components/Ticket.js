import styled from 'styled-components'

const TicketCardRoot = styled.div`
    display: flex;
    flex-direction: column;
    filter: drop-shadow(0px 7px 40px rgba(0, 0, 0, 0.1));
`

const TicketRip = styled.div`
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
        right: -64px;
        transform: translate(-50%, -50%) rotate(225deg);
        border: 8px solid transparent;
        border-top-color: white;
        border-right-color: white;
        border-radius: 100%;
    }
`

function TicketCard(props) {
    return <TicketCardRoot style={props.style}>
        <div style={{
            height: '132px',
            display: 'flex',
            alignItems: 'center',
            backgroundColor: 'white',
            borderRadius: '12px 12px 0 0',
            justifyContent: 'center',
            padding: '1.625rem 1.625rem 1.125rem 1.625rem'
        }}>
            {props.body}
        </div>

        <TicketRip>
            <div style={{
                borderTop: '2px dashed #90B7F1',
                width: '100%'
            }}></div>
        </TicketRip>

        <div style={{
            height: '132px',
            display: 'flex',
            alignItems: 'center',
            backgroundColor: 'white',
            borderRadius: '0 0 12px 12px',
            padding: '1.125rem 1.625rem 1.625rem 1.625rem'
        }}>
            {props.footer}
        </div>
    </TicketCardRoot>
}

export default TicketCard