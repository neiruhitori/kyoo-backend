import styled from 'styled-components'

const CardRoot = styled.div`
    box-shadow: 0px 7px 40px rgba(0, 0, 0, 0.1);
    border-radius: 12px;
    background-color: #FFFFFF;
    padding: 1.125rem;
`

function Card(props) {
    return <CardRoot style={props.style}>
        {props.children}
    </CardRoot>
}

export default Card;