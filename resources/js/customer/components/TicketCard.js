import styled from 'styled-components'

const TicketContainer = styled.div`
    filter: drop-shadow(0px 7px 40px rgba(0, 0, 0, 0.1));
`

const TicketCardRoot = styled.div`
    display: flex;
    flex-direction: column;
    overflow: hidden;

    > * {
        background-color: white;
    }

    > :first-child {
        border-radius: 12px 12px 0 0;
    }

    > :last-child {
        border-radius: 0 0 12px 12px;
    }
`

function TicketCard(props) {
    return <TicketContainer>
        <TicketCardRoot style={props.style}>
            {props.children}
        </TicketCardRoot>
    </TicketContainer>
}

export default TicketCard