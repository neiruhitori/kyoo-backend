import styled from 'styled-components'

const ChipWrapper = styled.div`
    border-radius: 6px;
    background-color: #103C7C;
    height: 1.75rem;
    display: inline-flex;
    align-items: center;
    color: #D6D6D6;
`

const ChipText = styled.span`
    padding: 0 0.5rem;
    font-size: 0.75rem;
`

function Chip(props) {
    return <ChipWrapper className={props.className}>
        <ChipText>{props.label}</ChipText>
    </ChipWrapper>
}

export default Chip