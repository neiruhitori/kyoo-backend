import styled from 'styled-components'
import { Children } from 'react';

const Wrapper1 = styled.div(() => ({
    userSelect:'none',
    marginRight:'0.5rem',
    cursor:'pointer'
}))
const Wrapper2 = styled.div(() => ({
    borderRadius:'5px', padding:'0.5rem', marginBottom:'0.5rem'
}))
const Wrapper3 = styled.div(() => ({
    fontSize:'small', textAlign:'center',
}))

function SlotServiceComponent(props) {
    return <Wrapper1  {...props}>
        <Wrapper2 style={{ 
            backgroundColor: props.selectedSlotId === props.slotId ? '#4a90e2' : '#f0f0f0',
            color:  props.usedSlot == 0 ? '#8e8e8e'
            : props.selectedSlotId === props.slotId
                ? '#fff'
                : '#000', }}>
            {props.startTime + ` - ` + props.endTime}
        </Wrapper2>
        <Wrapper3 style={{ color: props.usedSlot == 0 ? '#8e8e8e':'black' }}>
            {props.usedSlot + `/` + props.maxSlots + ` Slot`}
        </Wrapper3>
    </Wrapper1>
}

export default SlotServiceComponent