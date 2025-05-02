import styled from 'styled-components'
import { Children } from 'react';

const Wrapper = styled.div(() => ({
    backgroundColor:'#33A0FF', 
    padding:'3px 5px', 
    fill:'white', 
    borderRadius:'5px', 
    display:'flex', 
    alignItems:'center', 
    marginRight:'5px'
}))

function IconWrapper(props) {
    return <Wrapper  {...props}>
        {props.children}
    </Wrapper>
}

export default IconWrapper