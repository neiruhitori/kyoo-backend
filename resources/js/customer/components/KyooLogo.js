import styled from 'styled-components'

const KyooLogo = styled.img.attrs(() => ({
    src: '/img/logo-color.svg',
    alt: 'App logo'
}))`
    width: auto;
    height: ${props => props.width || '1.6rem'};
`

export default KyooLogo