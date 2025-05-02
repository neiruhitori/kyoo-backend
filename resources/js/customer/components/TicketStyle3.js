import styled from 'styled-components'

const TicketRipRoot = styled.div`
    height: 13rem;
    background-color: #103C7C ;
    position: relative;
    display: flex;
    align-items: center;
    overflow: visible; 
    justify-content: center;
    border-radius: 13px 13px 0px 0px;

    &::before,
    &::after {
        content: '';
        position: absolute;
        width: 32px;
        height: 35px;
        background: white;
        border-radius: 50%;
        top: 50%;
        transform: translateY(-50%);
    }

    &::before {
        left: -16px;
    }

    &::after {
        right: -16px;
    }
`;

const Wrapper = styled.div`
    display: flex;
    align-items: center;
    gap:1.5rem;
`
const Queue = styled.h4`
     background-color: white;
     color:black;
     padding: 1rem 0.5rem;
     margin-bottom: 0.5rem;
     border-radius: 10px;
    text-align: center;
`
const QueueNumber = styled.h2`
     background-color: white;
     color:black;
     padding:0.5rem;
     font-size: 3rem;
     border-radius: 10px;
     text-align: center;
`
const CurrentQueue = styled.h4`
     background-color: #33A0FF;
     color:white;
     padding: 1rem 0.5rem;
     margin-bottom: 0.5rem;
     border-radius: 10px;
    text-align: center;
`
const CurrentNumber = styled.h2`
     background-color: #33A0FF;
     color:white;
     padding:0.5rem;
     font-size: 3rem;
     border-radius: 10px;
     text-align: center;
`

export default function TicketRip(props) {
    return (
        <TicketRipRoot {...props}>
            <Wrapper>
                <div>
                    <Queue>Nomor Antrian</Queue>
                    <QueueNumber>{props?.queueNo}</QueueNumber>
                </div>
                <div>
                    <CurrentQueue>Antrian Sekarang</CurrentQueue>
                    <CurrentNumber>{props?.currentQueue}</CurrentNumber>
                </div>
            </Wrapper>
        </TicketRipRoot>
    );
}
