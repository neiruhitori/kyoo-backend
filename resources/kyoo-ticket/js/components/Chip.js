import styled from 'styled-components'

const ChipWrapper = styled.div`
    border-radius: 6px;
    background-color: #103C7C;
    height: 1.75rem;
    display: inline-flex;
    align-items: center;
`

const ChipText = styled.span`
    padding: 0 0.5rem;
    color: #D6D6D6;
    font-size: 0.75rem;
`

function Chip(props) {
    return <ChipWrapper>
        <ChipText>{props.label}</ChipText>
    </ChipWrapper>
}

export default Chip