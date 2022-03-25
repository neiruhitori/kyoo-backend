import styled from 'styled-components'

const KyooLogoRoot = styled.img.attrs(() => ({
    src: '/img/logo-color.svg',
    alt: 'App logo'
}))`
    width: auto;
    height: 1.6rem;
`

function KyooLogo() {
    return <KyooLogoRoot />
}

export default KyooLogo