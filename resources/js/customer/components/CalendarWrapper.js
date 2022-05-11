import styled from 'styled-components'

const CalendarWrapperRoot = styled.div(() => ({
    backgroundColor: 'rgba(0, 0, 0, 0.5)',
    position: 'fixed',
    top: 0,
    right: 0,
    bottom: 0,
    left: 0,
    display: 'flex',
    justifyContent: 'center',
    alignItems: 'center',
    zIndex: '9999'
}))

const CalendarCard  = styled.div(() => ({
    backgroundColor: '#FFFFFF',
    borderRadius: '12px'
}))

function CalendarWrapper(props) {
    return <CalendarWrapperRoot {...props}>
        <CalendarCard onClick={e => e.stopPropagation()}>
            <style>
                {`
                    .DayPicker-Day--today {
                        background-color: #E8F4FD;
                        color: #005AA3;
                    }
                `}
            </style>

            {props.children}
        </CalendarCard>
    </CalendarWrapperRoot>
}

export default CalendarWrapper