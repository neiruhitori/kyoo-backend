import styled from 'styled-components'

import BranchStatus from './BranchStatus'
import BulletIcon from '../icons/BulletIcon'

const OpenStatus = styled.span(() => ({
    display: 'inline-flex',
    alignItems: 'center',
    color: '#D21C1C'
}))
function BranchStatusClosed(props) {
    const closedText = typeof props.t === 'function' ? props.t('CLOSED') : 'TUTUP';
    return <BranchStatus style={props.style}>
        <OpenStatus>
            <BulletIcon color="#D21C1C" style={{
                width: '0.5rem',
                height: '0.5rem',
                marginRight: '0.375rem'
            }} />
            {closedText}
        </OpenStatus>
    </BranchStatus>
}

export default BranchStatusClosed