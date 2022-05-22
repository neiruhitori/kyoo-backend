import styled from 'styled-components'

const StyledButton = styled.button`
    font-family: Inter, sans-serif;
    background-color: #E8EDF1;
    color: #252A31;
    padding: 1rem 1.125rem;
    border: none;
    border-radius: 6px;
    outline: none;
`

const LinkButton = styled(StyledButton)`
    background-color: transparent;
    color: #007EC6;
`

const PrimaryButton = styled(StyledButton)`
    background-color: #007EC6;
    color: #FFFFFF;
`

function Button(props) {
    if (props.color === 'link') {
        return <LinkButton {...props}>
            {props.children}
        </LinkButton>
    }

    if (props.color === 'primary') {
        return <PrimaryButton {...props}>
            {props.children}
        </PrimaryButton>
    }

    return <StyledButton {...props}>
        {props.children}
    </StyledButton>
}

export default Button