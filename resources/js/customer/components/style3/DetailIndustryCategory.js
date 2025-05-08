import styled from 'styled-components'
import { Children } from 'react';

const Wrapper1 = styled.div(() => ({
    display: 'flex',
    alignItems: 'center',
    justifyContent: 'space-between',
    marginBottom: '0.5rem'
}))
const Wrapper2 = styled.div(() => ({
    borderRadius: '15px',
    backgroundColor:'#33A0FF',
    display: 'inline-flex',
    alignItems: 'center',
    color: '#fff',
    fontSize: '0.75rem',
    padding:' 0.25rem .75rem',
}))

function IndustryCategoryWrapper(props) {
    return <Wrapper1  {...props}>
        <Wrapper2>
             {props.children}
        </Wrapper2>
    </Wrapper1>
}

export default IndustryCategoryWrapper