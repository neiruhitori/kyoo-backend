import styled from 'styled-components'

const H2Root = styled.h2`
    font-weight: 700;
    font-size: 1.75rem;
    text-shadow: 0 1px 3px rgb(0 0 0 / 0.1), 0 1px 2px rgb(0 0 0 / 0.1);
`

function H2(props) {
    return <H2Root {...props}>
        {props.children}
    </H2Root>
}

export default H2