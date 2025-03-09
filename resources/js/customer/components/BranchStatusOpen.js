import styled from 'styled-components'

import BranchStatus from './BranchStatus'
import BulletIcon from '../icons/BulletIcon'

const OpenStatus = styled.span(() => ({
    display: 'inline-flex',
    alignItems: 'center',
    color: '#3EAF3F'
}))

const OpenTime = styled.span(() => ({
    marginLeft: '0.375rem'
}))

function BranchStatusOpen(props) {
    const openText = typeof props.t === 'function' ? props.t('OPEN') : 'BUKA';
    return <BranchStatus style={props.style}>
        <OpenStatus style={{
            marginRight: '0.375rem'
        }}>
            <BulletIcon color="#3EAF3F" style={{
                width: '0.5rem',
                height: '0.5rem',
                marginRight: '0.375rem'
            }} />
            {openText}
        </OpenStatus>|

        <OpenTime>
            {props.startTime} - {props.endTime}
        </OpenTime>
    </BranchStatus>
}

export default BranchStatusOpen