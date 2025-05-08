import styled from "styled-components";

const ModalBackdropWrapper = styled.div(() => ({
    position: 'fixed',
    top: 0,
    left: 0,
    right: 0,
    bottom: 0,
    backgroundColor: 'rgba(0, 0, 0, 0.5)', 
    display: 'flex',
    justifyContent: 'center',
    alignItems: 'flex-end', 
    zIndex: 1000
}))


function ModalBackdrop(props){
    return<ModalBackdropWrapper {...props}>
        {props.children}
    </ModalBackdropWrapper>
}

export default ModalBackdrop;