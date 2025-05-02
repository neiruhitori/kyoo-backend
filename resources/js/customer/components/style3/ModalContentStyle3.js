import styled from "styled-components";

const ModalContentWrapper = styled.div(() => ({
    backgroundColor: 'white',
    width: '100%',
    maxWidth: '420px',
    borderTopLeftRadius: '20px',
    borderTopRightRadius: '20px',
    padding: '2rem',
    minHeight: '40vh', 
    animation: 'slideUp 1s ease'
}))


function ModalContent(props){
    return<ModalContentWrapper {...props}>
        {props.children}
    </ModalContentWrapper>
}

export default ModalContent;