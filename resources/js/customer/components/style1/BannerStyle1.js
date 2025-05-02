import styled from 'styled-components'

const BannerWrapper = styled.div`
    background: linear-gradient(0deg, rgba(71, 85, 105, 0.20), rgba(71, 85, 105, 0.20)), url('${props => props.imageUrl ? '/storage/' + props.imageUrl : '/img/queue.jpeg'}');
    background-size: cover;
    height: 230px;
    position: relative;
    width: 100%;
    overflow: visible;
    display: flex;
    flex-direction: column;
`

function Banner(props) {
    return <BannerWrapper imageUrl={props.imageUrl} style={props.style}>
        {props.children}
    </BannerWrapper>
}

export default Banner