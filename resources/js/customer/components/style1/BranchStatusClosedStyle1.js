import styled from 'styled-components'

import BranchStatus from '../BranchStatus'
import BulletIcon from '../../icons/BulletIcon'

const OpenStatus = styled.span(() => ({
    display: 'inline-flex',
    alignItems: 'center',
    fontSize:'smaller',
    color: '#D21C1C'
}))
function BranchStatusClosed(props) {
    const closedText = typeof props.t === 'function' ? props.t('CLOSED') : 'TUTUP';
    return <>
        <OpenStatus>
            <BulletIcon color="#D21C1C" style={{
                width: '0.375rem',
                height: '0.375rem',
                marginRight: '0.2rem'
            }} />
            {closedText}
        </OpenStatus>
    </>
}

export default BranchStatusClosed