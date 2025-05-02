import styled from 'styled-components'
import { Children } from 'react';

const Wrapper1 = styled.div(() => ({
    padding: '1rem 0rem',
    minWidth: 70,
    margin: '0 8px',
    borderRadius: 8,
    textAlign: 'center',
    cursor: 'pointer',
    flexShrink: 0,
    userSelect:'none',
}))

function DateSlider(props) {
    return <Wrapper1  {...props}
            style={{    backgroundColor: props.displayStartDate?.toDateString() === props.day.toDateString() ? '#4a90e2' : '#f0f0f0',
                        color: props.displayStartDate?.toDateString() === props.day.toDateString() ? '#fff' : '#000', }}>
        {props.children}
    </Wrapper1>
}

export default DateSlider