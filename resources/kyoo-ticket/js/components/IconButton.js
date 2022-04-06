import styled from 'styled-components'

const IconButtonRoot = styled.button(() => ({
    display: 'flex',
    justifyContent: 'center',
    alignItems: 'center',
    padding: '0.5rem 1rem',
    background: 'transparent',
    border: 'none',
    cursor: 'pointer'
}))

function IconButton(props) {
    return <IconButtonRoot {...props}>
        {props.children}
    </IconButtonRoot>
}

export default IconButton
