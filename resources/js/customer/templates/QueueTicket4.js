import  { forwardRef } from 'react'
import InfoAlert from '../components/InfoAlert'
import { format } from 'date-fns'
import QRCode from 'react-qr-code';
import TicketCard from '../components/TicketCard';
import TicketRip from '../components/TicketRip';
import id from 'date-fns/locale/id'
import en  from 'date-fns/locale/en-US'
import useLocalization from '../hooks/useLocalization'


export default forwardRef(function OnsiteQueueTicket({ booking, branch, style }, ref) {
    const{t, locale} = useLocalization();
    const dateLocale = locale == 'id' ? id : en
    return <div ref={ref}>
    <TicketCard style={{
        padding: '1.7rem',
        ...style
    }}>
<div style={{
            padding: '1.75rem',
        }}>
                <div style={{
                textAlign:'center',
                marginBottom: '0.8rem',
                }}>
                    <h4 style={{
                        fontWeight: '700',
                        fontSize: '1.5rem',
                        marginBottom:'1rem'
                    }}>
                       {branch?.name}
                    </h4>
                    <QRCode value={booking.booking_code.toUpperCase() || ''} size={150}
                            style={{marginBottom:'1.2rem'}}/>

                    <h4 style={{
                        fontWeight: '700',
                        fontSize: '3rem',
                        color: '#103C7C',
                    }}>
                         {booking?.queue_no}
                    </h4>
                    <h4 style={{
                        fontWeight: '700',
                        fontSize: '2rem',
                        color: '#33A0FF',
                        marginBottom:'0.5rem'
                    }}>
                         {booking.booking_code.toUpperCase() || ''}
                    </h4>
                </div>
        </div>

        <TicketRip/>
        <div style={{ textAlign:'center', padding:'2.75rem' }}>
            <h5 style={{ 
                fontSize:'medium', 
                marginBottom:'1.2rem'
             }}>{booking?.service_name}</h5>

            <p style={{  
                marginBottom:'1.2rem'
             }}>{ booking?.date ? 
                format(new Date(booking?.date), 
                "dd MMMM yyyy", {locale:dateLocale})
                : '-'}</p>

            <p style={{  
                marginBottom:'1.2rem'
             }}>{booking?.start_time + ` - ` + booking?.end_time}</p>
        </div>
    </TicketCard>
    </div>
})
