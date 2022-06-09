import styled from 'styled-components'

const ChipWrapper = styled.div`
    border-radius: 6px;
    background-color: #103C7C;
    display: inline-flex;
    align-items: center;
    color: #D6D6D6;
    font-size: 0.75rem;
    padding: 0.5rem .75rem;
`

function Chip(props) {
    return <ChipWrapper className={props.className}>
        {props.label}
    </ChipWrapper>
}

export default Chip