import styled from 'styled-components';
import { format, formatBrowser } from '../utils/date';

const Wrapper = styled.div`
    display: grid;
    grid-template-columns: 10rem 10px 1fr; 
    align-items: start; 
    gap: 0.5rem;
    margin-bottom: 1rem;
`;

const Title = styled.h4`
    margin: 0;
    white-space: nowrap;
`;

const Content = styled.p`
    margin: 0;
    word-break: break-word;
`;

export default function TicketRip(props) {
    return (
        <div style={{ padding: '1.75rem' }} {...props}>
            <Wrapper>
                <Title>Nama Layanan</Title>
                <span>:</span>
                <Content>{props.booking?.service_name}</Content>
            </Wrapper>

            <Wrapper>
                <Title>Kode Booking</Title>
                <span>:</span>
                <Content>{props.booking?.booking_code}</Content>
            </Wrapper>

            <Wrapper>
                <Title>Tanggal</Title>
                <span>:</span>
                <Content>{format(formatBrowser(props.booking?.date))}</Content>
            </Wrapper>

            <Wrapper>
                <Title>Slot Waktu</Title>
                <span>:</span>
                <Content>{props.booking?.start_time + ` - ` + props.booking?.end_time}</Content>
            </Wrapper>
        </div>
    );
}
