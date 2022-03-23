import styled from 'styled-components'

const StyledHeader = styled.header`
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
    z-index: 10;
`

const LogoWrapper = styled.div`
    padding-right: 0.5rem;
    border-right: 1px solid #EEEEEE;
    margin-right: 0.75rem;
`

const LogoLink = styled.a.attrs(() => ({
    href: '#'
}))`
    display: flex;
    alignItems: center;
`

const StyledHeaderLogo = styled.img.attrs(() => ({
    src: '/img/logo-color.svg',
    alt: 'App logo'
}))`
    width: auto;
    height: 1.6rem;
`

function Header() {
    return <StyledHeader>
        <LogoWrapper>
            <LogoLink>
                <StyledHeaderLogo />
            </LogoLink>
        </LogoWrapper>

        <div>Booking Appointment</div>
    </StyledHeader>
}

export default Header