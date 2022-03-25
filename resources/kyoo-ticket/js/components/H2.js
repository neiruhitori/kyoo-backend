import styled from 'styled-components'

const H2Root = styled.h2`
    font-weight: 700;
    font-size: 1.75rem;
`

function H2(props) {
    return <H2Root {...props}>
        {props.children}
    </H2Root>
}

export default H2