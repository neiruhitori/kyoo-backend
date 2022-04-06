import styled from 'styled-components'

import BulletIcon from '../icons/BulletIcon'

const SliderIndicatorRoot = styled.div(() => ({
    display: 'flex',
    alignItems: 'center',
    justifyContent: 'center'
}))

const IndicatorItem = styled.span`
    margin-left: 0.375rem;

    &:first-child {
        margin-left: 0;
    }
`

function SliderIndicator(props) {
    const slideItems = []

    for (let i = 0; i < props.total; i++) {
        slideItems.push(
            <IndicatorItem key={i}>
                <BulletIcon color="#FFFFFF" width="0.5rem" height="0.5rem" style={{
                    opacity: i === props.active ? 1 : 0.5
                }} />
            </IndicatorItem>
        )
    }

    return <SliderIndicatorRoot style={props.style}>
        {slideItems}
    </SliderIndicatorRoot>
}

export default SliderIndicator