import styled from 'styled-components'

const MainContentWrapper = styled.div`
    background: url('/img/bgstyle3.jpg') right top / cover no-repeat fixed;
    padding: 9rem 2rem 1.375rem;
    flex: 1 1 0%;
    display: flex;
    flex-direction: column;
    border-radius:'0px 0px 0px 0px' ;
`

function MainContent(props) {
    return <MainContentWrapper {...props}>
        {props.children}
    </MainContentWrapper>
}


export default MainContent