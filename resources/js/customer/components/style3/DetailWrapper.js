import styled from 'styled-components'
import { Children } from 'react';

const Wrapper = styled.div(() => ({
        backgroundColor:'#33A0FF',
        padding:'1px 3.5px',
        marginLeft:'1.5rem',
        borderRadius:'15px',
        color: '#fff',
        display: 'flex',
        alignItems: 'center',
        fontSize: '0.75rem',
        textShadow: '0 1px 3px rgb(0 0 0 / 0.1), 0 1px 2px rgb(0 0 0 / 0.1)'
}))

function DetailWrapper(props) {
    return <Wrapper  {...props}>
        {props.children}
    </Wrapper>
}

export default DetailWrapper