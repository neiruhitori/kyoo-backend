import styled from 'styled-components'

const BannerWrapper = styled.div`
    padding: 2rem 1.75rem 3.25rem 1.75rem;
    background: linear-gradient(180deg, rgba(0, 0, 0, 0.21) 0%, #000000 142.65%), url('${props => props.imageUrl}'), #C4C4C4;
    background-size: cover;
    border-radius: 0 0 16px 16px;
    min-height: 216px;
    max-height: 260px;
    position: relative;
    width: 100%;
    overflow: visible;
`

function Banner(props) {
    return <BannerWrapper imageUrl={props.imageUrl} style={props.style}>
        {props.children}
    </BannerWrapper>
}

export default Banner