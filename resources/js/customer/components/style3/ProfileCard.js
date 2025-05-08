import styled from 'styled-components'
import { Children } from 'react';

const Wrapper = styled.div(() => ({
    marginBottom:'2.5rem', 
    position:'absolute', 
    backgroundColor:'white',
    zIndex:'1',
    top:'70%',
    margin:'0px 1.5rem',
    padding:'1.5rem 2rem',
    width:'90%',
    borderRadius:'10px'
}))

function ProfileCard(props) {
    return <Wrapper  {...props}>
        {props.children}
    </Wrapper>
}

export default ProfileCard