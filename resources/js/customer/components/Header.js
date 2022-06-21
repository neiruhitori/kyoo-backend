import styled from 'styled-components'

const HeaderRoot = styled.header`
    font-weight: 600;
    min-height: 3.2rem;
    display: flex;
    align-items: center;
    position: sticky;
    background-color: ${props => props.bgType === 'blur' ? 'rgba(0, 0, 0, 0.28)' : '#FFFFFF'};
    backdrop-filter: ${props => props.bgType === 'blur' ? 'blur(10px)' : 'none'};
    color: ${props => props.bgType === 'blur' ? '#FFFFFF' : '#000000'}; 
    top: 0;
    left: 0;
    right: 0;
    box-shadow: 0px 4px 23px rgba(0, 0, 0, 0.09);
    z-index: 999;
`

function Header(props) {
    return <HeaderRoot bgType={props.bgType || 'normal'} style={props.style}>
        {props.children}
    </HeaderRoot>
}

export default Header