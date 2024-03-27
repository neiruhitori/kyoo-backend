import { useState, useEffect, useRef } from 'react'
import { useMutation, useQuery, useQueryClient } from 'react-query'
import { useNavigate, useParams, Link } from 'react-router-dom'
import QRCode from 'react-qr-code';
import styled from 'styled-components'
import html2canvas from 'html2canvas'

import { getAppointmentOnsiteById } from '../../api/appointmentOnsite'
import { fetchBranch } from '../../api/branch'
import { createFeedback } from '../../api/feedback'
import usePromotions from '../../hooks/usePromotions'
import { formatBrowser, getDayName, getMonthAbrvName } from '../../utils/date'
import { getCookie } from '../../lib/helper'

import MainContent from '../../components/MainContent'
import TicketCard from '../../components/TicketCard'
import InfoAlert from '../../components/InfoAlert'
import KyooLogo from '../../components/KyooLogo'
import ChipWarning from '../../components/ChipWarning'
import ChipSuccess from '../../components/ChipSuccess'
import ChipDanger from '../../components/ChipDanger'
import Card from '../../components/Card'
import Rating from '../../components/Rating'
import Dialog from '../../components/Dialog'
import TicketRip from '../../components/TicketRip'
import Story from '../../components/Story'

import OnsiteQueueTicket from '../../templates/OnsiteQueueTicket'

import ArrowLeftIcon from '../../icons/ArrowLeftIcon'
import LocationIcon from '../../icons/LocationIcon'
import SaveIcon from '../../icons/SaveIcon'
import RedoIcon from '../../icons/RedoIcon'
import AngleRightIcon from '../../icons/AngleRightIcon'

const BranchLogo = styled.img`
    display: inline-block;
    height: 4.5rem;
`

const BookingTimeCard = styled.div`
    background: rgba(255, 255, 255, 0.04);
    box-shadow: 0px 7px 40px rgba(0, 0, 0, 0.1);
    border-radius: 12px;
    width: 86px;
    height: 86px;
    padding: .875rem;
    color: #007EC6;
`

const OnsiteChipSuccess = styled(ChipSuccess)`
    font-size: .875rem;
`

const OnsiteChipWarning = styled(ChipWarning)`
    font-size: .875rem;
`

const OnsiteChipDanger = styled(ChipDanger)`
    font-size: .875rem;
`

function TicketHead(props) {
    return <div style={{
        display: 'flex',
        alignItems: 'center',
        flexDirection: 'column',
        padding: '1.75rem'

    }}>
        <div style={{ marginBottom: '1rem' }}>
            <QRCode value={props.bookingCode.toUpperCase() || ''} size={100}/>
        </div>

        <p style={{
            fontSize: '1rem',
            color: '#7A7A7A',
            marginBottom: '.5rem',
            textAlign: 'center'
        }}>Kode Booking</p>

        <h2 style={{
            fontWeight: '700',
            fontSize: '3.625rem',
            color: '#103C7C',
            textAlign: 'center',
            textTransform: 'uppercase'
        }}>
            {props.bookingCode || 0}
        </h2>
    </div>
}

function TicketFooter(props) {
    return <div style={{
        display: 'flex',
        padding: '1.75rem'
    }}>
        <BookingTimeCard style={{
            marginRight: '1.625rem',
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

function AppointmentOnsiteBookingStatus(props) {
    const PAGE_TITLE = 'Status Antrian'
    const REFETCH_INTERVAL = 15000
    const CLIENT_ID = getCookie('client_id')

    const { branchId, bookingId } = useParams()
    const queryClient = useQueryClient()
    const ticketRef = useRef(null)
    const navigate = useNavigate()

    const [rating, setRating] = useState(0)
    const [isDialogShown, setIsDialogShown] = useState(false)
    const [isConfirmationDialogShown, setIsConfirmationDialogShown] = useState(false)
    const [isShowPromotion, setIsShowPromotion] = useState(true)

    let booking = null
    let branch = null
    let schedule = null
    let branchType = 'free'

    useEffect(() => {
        if (!sessionStorage.getItem('dialog-shown')) {
            setIsDialogShown(true)
            sessionStorage.setItem('dialog-shown', 'true')
        }

        window.Echo.channel(`onsite_queues.${CLIENT_ID}`)
            .listen('OnsiteQueueUpdated', () => {
                queryClient.invalidateQueries(['booking', bookingId])
            })

        return () => {
            sessionStorage.removeItem('dialog-shown')
            window.Echo.channel(`onsite_queues.${CLIENT_ID}`)
                .stopListening('OnsiteQueueUpdated')
        }
    }, [])

    const promotionsQuery = usePromotions(branchId)
    const bookingQuery = useQuery(['booking', bookingId], () => getAppointmentOnsiteById(bookingId), {
        enabled: promotionsQuery.isSuccess
    })
    const branchQuery = useQuery(['branch', branchId], () => fetchBranch(branchId), {
        enabled: bookingQuery.status === 'success'
    })
    const feedbackMutation = useMutation('feedback', (data) => createFeedback('onsite', bookingId, data))

    if (bookingQuery.status === 'success') {
        booking = bookingQuery.data?.data
    }

    if (bookingQuery.status === 'success' && branchQuery.status === 'success') {
        branch = branchQuery.data

        schedule = branch.schedule.find(v => {
            return v.day === getDayName(formatBrowser(booking.date), 'en')
        })
    }

    if (branch?.branch_type.is_premium) {
        branchType = 'premium'
    }

    function handleFeedbackClick() {
        feedbackMutation.mutate({
            rating,
            is_liked: false
        }, {
            onSuccess: ({ data }) => {
                booking.rating = data.rating
                queryClient.invalidateQueries(['booking', bookingId])
            }
        })
    }

    function handlePrintTicket(bookingCode) {
        html2canvas(ticketRef.current).then((canvas) => {
            const imgUrl = canvas.toDataURL('image/png');

            const a = document.createElement('a')
            a.href = imgUrl
            a.download = `${bookingCode}.png`
            a.click()

            setIsDialogShown(false)
        })
    }

    function handleStoryDone() {
        setIsShowPromotion(false)
    }

    return <>
        {promotionsQuery.isSuccess &&
        !!promotionsQuery.data.length &&
        isShowPromotion &&
        branch?.branch_configuration.promotion &&
        <Story
            stories={promotionsQuery.data}
            onDone={handleStoryDone}
            style={{
                position: 'absolute',
                top: '0',
                left: '0',
                right: '0',
                bottom: '0',
                zIndex: '99999'
            }}
        />}

        {!!booking && !!branch && <OnsiteQueueTicket
            ref={ticketRef}
            booking={booking}
            branch={branch}
            style={{
                position: 'absolute',
                bottom: '100%',
                left: 0,
                right: 0
            }}
        />}

        {!!booking && isDialogShown && <Dialog>
            <div style={{
                display: 'flex',
                width: '68px',
                height: '68px',
                borderRadius: '999999px',
                alignItems: 'center',
                justifyContent: 'center',
                backgroundColor: '#D0E9FB',
                margin: '0 auto',
                marginBottom: '1.5rem'
            }}>
                <SaveIcon color="#0172CB" style={{ width: '30px', height: 'auto' }} />
            </div>

            <div style={{
                marginBottom: '1.5rem'
            }}>
                <h4 style={{
                    textAlign: 'center',
                    fontSize: '1.3rem',
                    marginBottom: '1rem'
                }}>Simpan Antrianmu?</h4>
                <p style={{
                    textAlign: 'center',
                    margin: '0 auto',
                    lineHeight: '1.5'
                }}>Simpan tiket antrian agar tidak kehilangan informasi antrianmu</p>
            </div>

            <div style={{
                display: 'flex',
                justifyContent: 'center',
                gap: '1rem'
            }}>
                <button style={{
                    width: '142px',
                    padding: '.875rem',
                    borderRadius: '8px',
                    border: '1px solid #D8D8D8',
                    outline: 'none',
                    fontWeight: 'bold',
                    backgroundColor: '#FFFFFF',
                    fontSize: '1rem'
                }} onClick={() => handlePrintTicket(booking.booking_code)}>Simpan</button>

                <button style={{
                    width: '142px',
                    padding: '.875rem',
                    borderRadius: '8px',
                    border: 'none',
                    outline: 'none',
                    fontWeight: 'bold',
                    backgroundColor: '#D8D8D8',
                    fontSize: '1rem'
                }} onClick={() => setIsDialogShown(false)}>Tidak</button>
            </div>
        </Dialog>}

        {isConfirmationDialogShown && <Dialog>
            <div style={{
                marginBottom: '1.5rem'
            }}>
                <p style={{
                    textAlign: 'center',
                    margin: '0 auto',
                    lineHeight: '1.5'
                }}>Apakah Anda yakin ingin meninggalkan halaman ini?</p>
            </div>

            <div style={{
                display: 'flex',
                justifyContent: 'center',
                gap: '1rem'
            }}>
                <button style={{
                    width: '142px',
                    padding: '.875rem',
                    borderRadius: '8px',
                    border: 'none',
                    outline: 'none',
                    color: '#FFFFFF',
                    backgroundColor: '#007EC6',
                    fontSize: '1rem'
                }} onClick={() => navigate(`/customer/${branchId}/onsite/services/two-layer`)}>Iya</button>

                <button style={{
                    width: '142px',
                    padding: '.875rem',
                    borderRadius: '8px',
                    border: 'none',
                    outline: 'none',
                    backgroundColor: '#D8D8D8',
                    fontSize: '1rem'
                }} onClick={() => setIsConfirmationDialogShown(false)}>Tidak</button>
            </div>
        </Dialog>}

        <div style={{
            display: 'flex',
            alignItems: 'center'
        }}>
            <button
                type="button"
                style={{
                    display: 'flex',
                    justifyContent: 'center',
                    alignItems: 'center',
                    height: '3.2rem',
                    outline: 'none',
                    backgroundColor: 'transparent',
                    border: 'none',
                    padding: '.85rem 1.375rem'
                }}
                onClick={() => setIsConfirmationDialogShown(true)}
            >
                <ArrowLeftIcon />
            </button>

            <div style={{
                textTransform: 'capitalize',
                textAlign: 'center',
                flex: '1 1 0%'
            }}>{PAGE_TITLE}</div>

            <button
                type="button"
                style={{
                    display: 'flex',
                    justifyContent: 'center',
                    alignItems: 'center',
                    height: '3.2rem',
                    outline: 'none',
                    backgroundColor: 'transparent',
                    border: 'none',
                    padding: '.85rem 1.375rem'
                }}
                onClick={() => location.reload()}
            >
                <RedoIcon />
            </button>
        </div>

        {bookingQuery.status === 'success' && <MainContent>
            <div>
                {!!branch?.logo && <div style={{
                    textAlign: 'center',
                    marginBottom: '1.5rem'
                }}>
                    <BranchLogo src={`/storage/${branch.logo}`}/>
                </div>}

                {branchQuery.status === 'success' && <TicketCard style={{
                    marginBottom: '1.5rem'
                }}>
                    <TicketHead bookingCode={booking.booking_code} />

                    <TicketRip />

                    <TicketFooter
                        serviceName={booking.service_name}
                        bookingDate={booking.date}
                        branch={branch}
                        schedule={booking}
                    />
                </TicketCard>}
            </div>

            <div style={{
                marginBottom: '1.5rem',
                textAlign: 'center'
            }}>
                <Link to="detail" style={{
                    padding: '.5rem',
                    color: '#0161AC',
                    display: 'inline-flex',
                    alignItems: 'center',
                    justifyContent: 'center'
                }}>
                    Lihat Detail Antrian
                    <AngleRightIcon color="#0161AC" style={{
                        marginLeft: '.5rem'
                    }} />
                </Link>
            </div>

            {branchType === 'premium' && booking.status === 'end served' && <Card style={{
                marginBottom: '1.5rem',
                padding: '1.625rem'
            }}>
                <p style={{
                    textAlign: 'center'
                }}>Seberapa puas Anda terhadap layanan kami?</p>

                <div style={{
                    marginTop: '1.125rem'
                }}>
                    <Rating
                        rate={booking.rating || rating}
                        onRateClick={rate => {
                            if (!booking.rating) setRating(rate)
                        }}
                    />
                </div>

                {!booking.rating && <div style={{
                    textAlign: 'center',
                    marginTop: '1.125rem'
                }}>
                    <button type="submit" style={{
                        padding: '.625rem 1.125rem',
                        borderRadius: '14px',
                        color: '#FFFFFF',
                        backgroundColor: '#007EC6',
                        border: 'none'
                    }} onClick={handleFeedbackClick}>Kirim Feedback</button>
                </div>}
            </Card>}

            <InfoAlert>
                <h4 style={{
                    fontSize: '1rem',
                    marginBottom: '.375rem',
                    textTransform: 'capitalize'
                }}>Informasi Antrian Onsite</h4>

                <p style={{
                    lineHeight: '1.5',
                }}>
                    Anda harus menyimpan kode booking untuk mendapatkan nomor antrian sesuai dengan hari pemesanan Anda.
                </p>
            </InfoAlert>
        </MainContent>}

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
    </>
}

export default AppointmentOnsiteBookingStatus
