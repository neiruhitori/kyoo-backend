import { useEffect } from 'react'
import { useQuery } from 'react-query'
import { Link, useParams }  from 'react-router-dom'

import { formatBrowser, formatTime, format } from "../../utils/date"
import { getBooking } from '../../api/booking'
import { fetchBranch } from '../../api/branch'

import Header from '../../components/Header'
import MainContent from '../../components/MainContent'
import Card from '../../components/Card'

import ArrowLeftIcon from '../../icons/ArrowLeftIcon'

export default function BookingDetail() {
    const PAGE_TITLE = 'Detail Antrian'

    const { bookingId, branchId } = useParams()

    const bookingQuery = useQuery(['booking', bookingId], () => getBooking('onsite', bookingId))
    let booking = null

    if (bookingQuery.status === 'success') {
        booking = bookingQuery.data?.data
    }

    const branchQuery = useQuery(['branch', branchId], () => fetchBranch(branchId))
    let branch = null

    if (branchQuery.status === 'success') {
        branch = branchQuery.data
    }
    
    useEffect(() => {
        return () => {
            sessionStorage.setItem('dialog-shown', 'true')
        }
    }, [])

    return <>
        <Header style={{
            justifyContent: 'space-between'
        }}>
            <div style={{
                flex: '1 1 0%',
                display: 'flex'
            }}>
                <div style={{
                    marginRight:' 0.75rem'
                }}>
                    <Link to={-1} style={{
                        display: 'flex',
                        justifyContent: 'center',
                        alignItems: 'center'
                    }}>
                        <ArrowLeftIcon />
                    </Link>
                </div>

                <div style={{ textTransform: 'capitalize' }}>{PAGE_TITLE}</div>
            </div>
        </Header>

        <MainContent style={{
            flex: '1 1 0%',
            height: '100%',
        }}>
            <div style={{
                flexGrow: '1'
            }}>
                <Card style={{
                    marginBottom: '1.625rem'
                }}>
                    <h4 style={{
                        marginBottom: '1.625rem'
                    }}>Detail Booking</h4>

                    <div>
                        <div style={{
                            display: 'flex',
                            fontSize: '.875rem',
                            marginBottom: '1.125rem'
                        }}>
                            <div style={{
                                flex: '1',
                                color: '#A5A5A5'
                            }}>Tanggal Antri</div>

                            <div style={{
                                flex: '1',
                                color: '#103C7C',
                                fontWeight: '600'
                            }}>
                                {format(formatBrowser(booking?.date))}
                            </div>
                        </div>

                        <div style={{
                            display: 'flex',
                            fontSize: '.875rem',
                            marginBottom: '1.125rem'
                        }}>
                            <div style={{
                                flex: '1',
                                color: '#A5A5A5'
                            }}>Kode Unik</div>

                            <div style={{
                                flex: '1',
                                color: '#103C7C',
                                fontWeight: '600'
                            }}>
                                {booking?.booking_code.toUpperCase()}
                            </div>
                        </div>

                        <div style={{
                            display: 'flex',
                            fontSize: '.875rem',
                            marginBottom: '1.125rem'
                        }}>
                            <div style={{
                                flex: '1',
                                color: '#A5A5A5'
                            }}>Nama Cabang</div>

                            <div style={{
                                flex: '1',
                                color: '#103C7C',
                                fontWeight: '600'
                            }}>
                                {branch?.name}
                            </div>
                        </div>

                        <div style={{
                            display: 'flex',
                            fontSize: '.875rem',
                            marginBottom: '1.125rem'
                        }}>
                            <div style={{
                                flex: '1',
                                color: '#A5A5A5'
                            }}>Layanan</div>

                            <div style={{
                                flex: '1',
                                color: '#103C7C',
                                fontWeight: '600'
                            }}>
                                {booking?.service_name}
                            </div>
                        </div>

                        <div style={{
                            display: 'flex',
                            fontSize: '.875rem',
                            marginBottom: '1.125rem'
                        }}>
                            <div style={{
                                flex: '1',
                                color: '#A5A5A5'
                            }}>Waktu</div>

                            <div style={{
                                flex: '1',
                                color: '#103C7C',
                                fontWeight: '600'
                            }}>
                                {formatTime(formatBrowser(booking?.date))}
                            </div>
                        </div>
                    </div>
                </Card>

                <Card style={{
                    marginBottom: '1.625rem'
                }}>
                    <h4 style={{
                        marginBottom: '1.625rem'
                    }}>Detail Pengunjung</h4>

                    <div>
                        <div style={{
                            display: 'flex',
                            fontSize: '.875rem',
                            marginBottom: '1.125rem'
                        }}>
                            <div style={{
                                flex: '1',
                                color: '#A5A5A5'
                            }}>Nama</div>

                            <div style={{
                                flex: '1',
                                color: '#103C7C',
                                fontWeight: '600'
                            }}>
                                {booking?.name}
                            </div>
                        </div>

                        <div style={{
                            display: 'flex',
                            fontSize: '.875rem',
                            marginBottom: '1.125rem'
                        }}>
                            <div style={{
                                flex: '1',
                                color: '#A5A5A5'
                            }}>Email</div>

                            <div style={{
                                flex: '1',
                                color: '#103C7C',
                                fontWeight: '600'
                            }}>
                                {booking?.email}
                            </div>
                        </div>

                        <div style={{
                            display: 'flex',
                            fontSize: '.875rem',
                            marginBottom: '1.125rem'
                        }}>
                            <div style={{
                                flex: '1',
                                color: '#A5A5A5'
                            }}>No. Telepon</div>

                            <div style={{
                                flex: '1',
                                color: '#103C7C',
                                fontWeight: '600'
                            }}>
                                {booking?.phone}
                            </div>
                        </div>
                    </div>
                </Card>
            </div>
        </MainContent>
    </>
}