import styled from 'styled-components'

import BranchStatus from '../BranchStatus'
import BulletIcon from '../../icons/BulletIcon'

const OpenStatus = styled.span(() => ({
    display: 'inline-flex',
    alignItems: 'center',
    fontSize:'smaller',
    color: '#3EAF3F'
}))

const OpenTime = styled.span(() => ({
    marginLeft: '0.375rem',
    fontSize:'smaller',
    
}))

function BranchStatusOpenStyle1(props) {
    const openText = typeof props.t === 'function' ? props.t('OPEN') : 'BUKA';
    return <>
        <OpenStatus style={{
            marginRight: '0.375rem'
        }}>
            <BulletIcon color="#3EAF3F" style={{
                width: '0.375rem',
                height: '0.375rem',
                marginRight: '0.2rem'
            }} />
            {openText}
        </OpenStatus>|

        <OpenTime>
            {props.startTime} - {props.endTime}
        </OpenTime>
        </>
}

export default BranchStatusOpenStyle1