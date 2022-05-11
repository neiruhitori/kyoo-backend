import styled from 'styled-components'

const infoColor = '#0172CB'
const errorColor = '#D21C1C'

const StyledTextField = styled.div`
    width: 100%;
`

const InputGroup = styled.div`
    border-radius: 6px;
    overflow: hidden;
    background-color: #EFF2F5;
    box-sizing: border-box;
    border: 1px solid ${props => props.error ? errorColor : '#EFF2F5' };
    display: flex;

    &:focus-within {
        border-color: ${props => props.error ? errorColor : infoColor};
    }
`

const StyledInput = styled.input`
    background: transparent;
    padding: 1rem;
    padding-right: 0;
    font-size: 1rem;
    flex: 1 1 0%;
    border: none;
    outline: none;
    
    ::placeholder {
        color: #697D95;
        font-weight: 500;
        font-family: Inter, sans-serif;
        font-size: 1rem;
    }
`

const StyledLabel = styled.label`
    font-size: 0.875rem;
    display: inline-block;
    margin-bottom: 0.375rem;
`

const StyledHelperText = styled.div`
    font-size: 0.75rem;
    color: ${props => props.error ? errorColor : infoColor};
    margin-top: 0.375rem;
    display: flex;
    align-items: center;
    vertical-align: middle;
`

function TextField(props) {
    const svgWrapperStyle = {
        display: 'inline-flex',
        alignItems: 'center'
    }

    const svgStyle = {
        width: '1.25em',
        height: '1.25em',
        fill: props.error ? errorColor : infoColor,
        marginRight: '0.375rem'
    }

    const errorIcon = <path d="M256 0C114.6 0 0 114.6 0 256s114.6 256 256 256s256-114.6 256-256S397.4 0 256 0zM232 152C232 138.8 242.8 128 256 128s24 10.75 24 24v128c0 13.25-10.75 24-24 24S232 293.3 232 280V152zM256 400c-17.36 0-31.44-14.08-31.44-31.44c0-17.36 14.07-31.44 31.44-31.44s31.44 14.08 31.44 31.44C287.4 385.9 273.4 400 256 400z"/>
    const infoIcon = <path d="M256 0C114.6 0 0 114.6 0 256s114.6 256 256 256s256-114.6 256-256S397.4 0 256 0zM256 128c17.67 0 32 14.33 32 32c0 17.67-14.33 32-32 32S224 177.7 224 160C224 142.3 238.3 128 256 128zM296 384h-80C202.8 384 192 373.3 192 360s10.75-24 24-24h16v-64H224c-13.25 0-24-10.75-24-24S210.8 224 224 224h32c13.25 0 24 10.75 24 24v88h16c13.25 0 24 10.75 24 24S309.3 384 296 384z"/>

    const helperIcon = props.error ? errorIcon : infoIcon

    const inputProps = {
        type: props.type || 'text',
        value: props.value,
        readOnly: props.readOnly,
        placeholder: props.placeholder,
        onChange: props.onChange
    }

    return <StyledTextField style={props.style}>
        <StyledLabel>{props.label}</StyledLabel>

        <InputGroup error={props.error}>
            <StyledInput {...inputProps} error={props.error} />
            {props.endAdornment}
        </InputGroup>

        {props.helperText && <StyledHelperText error={props.error}>
            <span style={svgWrapperStyle}>
                <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" viewBox="0 0 512 512" style={svgStyle}>
                    {helperIcon}
                </svg>
            </span>

            <span>
                {props.helperText}
            </span>
        </StyledHelperText>}
    </StyledTextField>
}

export default TextField