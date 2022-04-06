import styled from 'styled-components'

import BulletIcon from '../icons/BulletIcon'

const BranchStatusRoot = styled.div(() => ({
    display: 'inline-flex',
    alignItems: 'center',
    justifyContent: 'center',
    backgroundColor: 'lightgray',
    borderRadius: '6px',
    fontSize: '0.875rem',
    height: '2.125rem',
    color: '#FFFF',
    background: 'rgba(0, 0, 0, 0.1)',
    boxShadow: '16px -4px 40px rgba(0, 0, 0, 0.1)',
    backdropFilter: 'blur(16px)'
}))

const OpenStatus = styled.span(({ isOpen }) => ({
    paddingLeft: '0.625rem',
    paddingRight: '0.375rem',
    display: 'inline-flex',
    alignItems: 'center',
    color: isOpen ? '#3EAF3F' : '#D21C1C'
}))

const OpenTime = styled.span`
    padding-right: 0.625rem;
    padding-left: 0.375rem;
`

function BranchStatus(props) {
    return <BranchStatusRoot style={props.style}>
        <OpenStatus isOpen={props.isOpen}>
            <BulletIcon color={props.isOpen ? '#3EAF3F' : '#D21C1C'} style={{
                width: '0.5rem',
                height: '0.5rem',
                marginRight: '0.375rem'
            }} />
            {props.isOpen ? 'BUKA' : 'TUTUP'}
        </OpenStatus>|
        <OpenTime>
            {props.startTime} - {props.endTime}
        </OpenTime>
    </BranchStatusRoot>
}

export default BranchStatus