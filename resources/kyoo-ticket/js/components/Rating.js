import styled from 'styled-components'

import StarIcon from '../icons/StarIcon'

const activeColor = '#FFCC48'
const inactiveColor = '#EFF2F5'

const RatingRoot = styled.div`
    display: flex;
    align-items: center;
    justify-content: center;
    gap: .625rem;
`

const RatingItem = styled.button`
    display: inline-flex;
    align-items: center;
    padding: 0;
    background: none;
    border: none;
`

export default function Rating(props)  {
    const rates = []

    for (let i = 1; i <= 5; i++) {
        rates.push(<RatingItem onClick={() => props.onRateClick(i)} key={i}>
            <StarIcon width="46" height="46" color={i <= props.rate ? activeColor : inactiveColor} />
        </RatingItem>)
    }

    return <RatingRoot>
        {rates}
    </RatingRoot>
}