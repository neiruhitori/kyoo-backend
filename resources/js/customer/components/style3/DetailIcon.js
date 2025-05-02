import styled from 'styled-components'
import { Children } from 'react';

const Wrapper1 = styled.div(() => ({
    display: 'flex', 
    flexDirection: 'column', 
    alignItems: 'center',
    justifyContent: 'center',
    cursor:'pointer'
}))
const Wrapper2 = styled.div(() => ({
    backgroundColor: '#fff',
    borderRadius: '100%',
    padding: '0.8rem',
    display: 'flex',
    alignItems: 'center',
    justifyContent: 'center',
 }))

const Wrapper3 = styled.p(() => ({
   color:'white', 
   marginTop:'0.5rem'
 }))

function DetailIconWrapper(props) {
    return <Wrapper1  {...props}>
        <Wrapper2>
             {props.children}
        </Wrapper2>
        <Wrapper3>
            {props.label}
        </Wrapper3>
    </Wrapper1>
}

export default DetailIconWrapper