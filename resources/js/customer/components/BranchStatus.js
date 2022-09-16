import styled from 'styled-components'

const BranchStatusRoot = styled.div(() => ({
    paddingLeft: '0.625rem',
    paddingRight: '0.625rem',
    display: 'inline-flex',
    alignItems: 'center',
    justifyContent: 'center',
    backgroundColor: 'lightgray',
    borderRadius: '6px',
    fontSize: '0.875rem',
    height: '2.125rem',
    color: '#FFFF',
    background: 'rgba(0, 0, 0, 0.2)',
    boxShadow: '16px -4px 40px rgba(0, 0, 0, 0.1)',
    backdropFilter: 'blur(16px)'
}))

function BranchStatus(props) {
    return <BranchStatusRoot style={props.style}>
        {props.children}
    </BranchStatusRoot>
}

export default BranchStatus