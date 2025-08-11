import styled from 'styled-components'

const activeColor = '#103C7C' // Biru
const inactiveBg = 'transparent'

const RatingRoot = styled.div`
    display: flex;
    align-items: center;
    justify-content: center;
    gap: .5rem;
`

const RatingItem = styled.button`
    display: flex;
    width: 2.5rem;
    height: 2rem;
    align-items: center;
    justify-content: center;
    padding: 0;
    background: ${({ active }) => (active ? activeColor : inactiveBg)};
    color: ${({ active }) => (active ? '#fff' : activeColor)};
    border: 2px solid ${activeColor};
    border-radius: 50%; /* bikin lingkaran */
    cursor: pointer;
    font-weight: bold;
    transition: background 0.2s, color 0.2s;

    &:hover {
        background: ${({ active }) => (active ? activeColor : '#e6eefc')};
    }
`

export default function Rating(props) {
    const rates = []

    for (let i = 1; i <= 10; i++) {
        rates.push(
            <RatingItem
                key={i}
                active={props.rate === i}
                onClick={() => props.onRateClick(i)}
            >
                {i}
            </RatingItem>
        )
    }

    return <RatingRoot>{rates}</RatingRoot>
}
