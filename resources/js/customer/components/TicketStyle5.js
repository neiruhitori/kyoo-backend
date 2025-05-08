import styled from 'styled-components';
import { format } from 'date-fns';
import useLocalization from '../hooks/useLocalization';
import id from 'date-fns/locale/id'
import en  from 'date-fns/locale/en-US';

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
    const {t, locale} = useLocalization();
    const dateLocale = locale == 'id' ? id : en
    return (
        <div style={{ padding: '1.75rem' }} {...props}>
            <Wrapper>
                <Title>{t('Service Name')}</Title>
                <span>:</span>
                <Content>{props.booking?.service_name}</Content>
            </Wrapper>

            <Wrapper>
                <Title>{t('Booking Code')}</Title>
                <span>:</span>
                <Content>{props.booking?.booking_code.toUpperCase()}</Content>
            </Wrapper>

            <Wrapper>
                <Title>{t('Date')}</Title>
                <span>:</span>
                <Content>
                    {format(new Date(props.booking?.date), 
                            "dd MMMM yyyy", {locale:dateLocale})}
                </Content>
            </Wrapper>

            <Wrapper>
                <Title>{t('Time Slot')}</Title>
                <span>:</span>
                <Content>{props.booking?.start_time + ` - ` + props.booking?.end_time}</Content>
            </Wrapper>
        </div>
    );
}
