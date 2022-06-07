import styled from 'styled-components'

import Card from './Card'

const DialogRoot = styled.div(() => ({
    backgroundColor: 'rgba(0, 0, 0, 0.5)',
    position: 'fixed',
    top: '0',
    right: '0',
    bottom: '0',
    left: '0',
    display: 'flex',
    justifyContent: 'center',
    alignItems: 'center',
    zIndex: '9999'
}))

export default function Dialog(props) {
    return <DialogRoot>
        <Card style={{
            padding: '2.5rem',
            maxWidth: '382px'
        }}>
            {props.children}
        </Card>
    </DialogRoot>
}