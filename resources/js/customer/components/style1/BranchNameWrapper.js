import styled from 'styled-components'
import { Children } from 'react';

const Wrapper = styled.h4(() => ({
    maxWidth:'250px',
    whiteSpace:'nowrap',
    textOverflow:'ellipsis',
    overflow:'hidden',
    fontWeight: '700',
    marginBottom: '0.5rem',
    fontSize: '1.3rem',
    textShadow: '0 1px 3px rgb(0 0 0 / 0.1), 0 1px 2px rgb(0 0 0 / 0.1)',
    color: '#092044'
}))

function BranchNameWrapper(props) {
    return <Wrapper  {...props}>
        {props.children}
    </Wrapper>
}

export default BranchNameWrapper