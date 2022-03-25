import styled from 'styled-components'

const HeaderRoot = styled.header`
    font-weight: 600;
    height: 3.2rem;
    display: flex;
    padding: 0 1.375rem;
    align-items: center;
    position: sticky;
    background-color: #FFFFFF;
    top: 0;
    width: 100%;
    box-shadow: 0px 4px 23px rgba(0, 0, 0, 0.09);
    z-index: 999;
`

function Header(props) {
    return <HeaderRoot>
        {props.children}
    </HeaderRoot>
}

export default Header