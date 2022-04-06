import { useQuery } from 'react-query'
import { Link, useParams } from 'react-router-dom'
import styled from 'styled-components'

import { getBooking } from '../../api/booking'
import { fetchBranch } from '../../api/branch'
import { formatBrowser, getDayName, getMonthAbrvName } from '../../utils/date'

import MainContent from '../../components/MainContent'
import TicketCard from '../../components/Ticket'
import InfoAlert from '../../components/InfoAlert'
import KyooLogo from '../../components/KyooLogo'

import ArrowLeftIcon from '../../icons/ArrowLeftIcon'
import LocationIcon from '../../icons/LocationIcon'

const BranchLogo = styled.img`
    display: inline-block;
    height: 4.5rem;
`

const BookingTimeCard = styled.div`
    background: rgba(255, 255, 255, 0.04);
    box-shadow: 0px 7px 40px rgba(0, 0, 0, 0.1);
    border-radius: 12px;
    width: 84px;
    height: 84px;
    padding: .875rem;
    color: #007EC6;
`

function TicketBody(props) {
    return <div>
        <p style={{
            fontSize: '1.125rem',
            color: '#7A7A7A',
            marginBottom: '1.125rem',
            textAlign: 'center'
        }}>Nomor Antrian</p>

        <h2 style={{
            fontWeight: '700',
            fontSize: '3.125rem',
            color: '#103C7C',
            textAlign: 'center'
        }}>
            {props.queueNo || 0}
        </h2>
    </div>
}

function TicketFooter(props) {
    return <div style={{
        display: 'flex',
    }}>
        <BookingTimeCard style={{
            marginRight: '1.625rem'
        }}>
            <h3 style={{
                fontSize: '1.625rem',
                fontWeight: '700'
            }}>{formatBrowser(props.bookingDate).getDate()}</h3>
            <p style={{
                fontSize: '.875rem',
                fontWeight: '600'
            }}>{getMonthAbrvName(formatBrowser(props.bookingDate))}</p>

            <div style={{
                fontSize: '.625rem',
                marginTop: '.5rem'
            }}>
                {props.schedule?.start_time.slice(0, 5)} - {props.schedule?.end_time.slice(0, 5)}
            </div>
        </BookingTimeCard>

        <div style={{
            flex: '1 1 0%'
        }}>
            <h4>{props.serviceName}</h4>

            <div style={{
                display: 'flex',
                marginTop: '1rem'
            }}>
                <LocationIcon color="#007EC6" height="1.25rem" width="1.25rem" />

                <div style={{
                    fontSize: '.875rem',
                    color: '#7A7A7A',
                    paddingLeft: '.625rem',
                    lineHeight: '1.5'
                }}>
                    {props.branch?.name}
                    <p>{props.branch?.address}</p>
                </div>
            </div>
        </div>
    </div>
}

function OnsiteBookingStatus() {
    const PAGE_TITLE = 'Status Antrian Onsite'
    const { branchId, bookingId } = useParams()

    let booking = null
    let branch = null
    let schedule = null

    const bookingQuery = useQuery('booking', () => getBooking('onsite', bookingId))
    const branchQuery = useQuery('branch', () => fetchBranch(branchId), {
        enabled: bookingQuery.status === 'success'
    })

    if (bookingQuery.status === 'success') {
        booking = bookingQuery.data?.data
    }

    if (bookingQuery.status === 'success' && branchQuery.status === 'success') {
        branch = branchQuery.data

        schedule = branch.schedule.find(v => {
            return v.day === getDayName(formatBrowser(booking.date), 'en')
        })
    }

    return <>
        <div style={{
            height: '3.2rem',
            padding: '0 1.375rem',
            display: 'flex',
            alignItems: 'center'
        }}>
            <Link to={`/kyooTicket/onsite/${branchId}/services`} style={{
                display: 'flex',
                justifyContent: 'center',
                alignItems: 'center'
            }}>
                <ArrowLeftIcon />
            </Link>

            <div style={{ textTransform: 'capitalize', textAlign: 'center', flex: '1 1 0%' }}>{PAGE_TITLE}</div>
        </div>

        <MainContent>
            {branchQuery.status === 'success' && <div>
                {!!branch?.logo && <div style={{
                    textAlign: 'center',
                    marginBottom: '1.5rem'
                }}>
                    <BranchLogo src={`/storage/${branch.logo}`}/>
                </div>}

                {bookingQuery.status === 'success' && branchQuery.status === 'success' && <TicketCard
                    body={<TicketBody queueNo={booking.queue_no} />}
                    footer={<TicketFooter
                        serviceName={booking.service_name}
                        bookingDate={booking.date}
                        branch={branch}
                        schedule={schedule}
                    />}
                    style={{
                        marginBottom: '2rem'
                    }}
                />}
            </div>}

            <InfoAlert>
                <h4 style={{
                    fontSize: '1rem',
                    marginBottom: '.375rem',
                    textTransform: 'capitalize'
                }}>Informasi Antrian Onsite</h4>

                <p style={{
                    lineHeight: '1.5',
                }}>
                    Anda akan dipanggil oleh counter yang Anda pilih sesuai dengan nomor antrian Anda
                </p>
            </InfoAlert>

            <div style={{
                display: 'flex',
                fontSize: '.875rem',
                color: '#7A7A7A',
                justifyContent: 'center',
                alignItems: 'center',
                padding: '1.125rem 0',
                marginTop: 'auto'
            }}>
                Powered by
                <KyooLogo style={{ marginLeft: '0.5rem' }} />
            </div>
        </MainContent>
    </>
}

export default OnsiteBookingStatus