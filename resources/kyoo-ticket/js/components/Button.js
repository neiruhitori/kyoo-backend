import styled from 'styled-components'

const StyledButton = styled.button`
    font-family: Inter, sans-serif;
    background-color: ${props => props.color == 'primary' ? '#007EC6' : '#E8EDF1'};
    color: ${props => props.color === 'primary' ? '#FFFFFF': '#252A31'};
    padding: 1rem 1.125rem;
    border: none;
    border-radius: 6px;
    outline: none;
`

function Button(props) {
    return <StyledButton {...props}>
        {props.children}
    </StyledButton>
}

export default Button