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

    const { bookingId, branchId, queueType } = useParams()

    const bookingQuery = useQuery(['booking', bookingId], () => getBooking(queueType, bookingId))
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
        <Header>
            <div style={{
                height: '3.2rem',
                display: 'flex'
            }}>
                <Link to={-1} style={{
                    display: 'flex',
                    justifyContent: 'center',
                    alignItems: 'center',
                    padding: '.85rem 1.375rem'
                }}>
                    <ArrowLeftIcon />
                </Link>
            </div>

            <div style={{
                textTransform: 'capitalize',
                flex: '1 1 0%'
            }}>{PAGE_TITLE}</div>
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

                        {queueType !== 'appointment-onsite' ?
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
                            :
                            <div style={{
                                display: 'flex',
                                fontSize: '.875rem',
                                marginBottom: '1.125rem'
                            }}>
                                <div style={{
                                    flex: '1',
                                    color: '#A5A5A5'
                                }}>Tanggal</div>

                                <div style={{
                                    flex: '1',
                                    color: '#103C7C',
                                    fontWeight: '600'
                                }}>
                                    {booking?.date}
                                </div>
                            </div>
                        }
                    </div>
                </Card>

                {branch?.branch_configuration.template_booking_form === 'standard-form' ?
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
                    :
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
                                }}>Tanggal lahir</div>

                                <div style={{
                                    flex: '1',
                                    color: '#103C7C',
                                    fontWeight: '600'
                                }}>
                                    {booking?.date_of_birth}
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
                                }}>Alamat</div>

                                <div style={{
                                    flex: '1',
                                    color: '#103C7C',
                                    fontWeight: '600'
                                }}>
                                    {booking?.address}
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

                            <div style={{
                                display: 'flex',
                                fontSize: '.875rem',
                                marginBottom: '1.125rem'
                            }}>
                                <div style={{
                                    flex: '1',
                                    color: '#A5A5A5'
                                }}>Nomer Cadangan</div>

                                <div style={{
                                    flex: '1',
                                    color: '#103C7C',
                                    fontWeight: '600'
                                }}>
                                    {booking?.emergency_number}
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
                                }}>NIK/Passport number</div>

                                <div style={{
                                    flex: '1',
                                    color: '#103C7C',
                                    fontWeight: '600'
                                }}>
                                    {booking?.passport_number}
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
                                }}>Alasan Kunjungan</div>

                                <div style={{
                                    flex: '1',
                                    color: '#103C7C',
                                    fontWeight: '600'
                                }}>
                                    {booking?.reason_for_visit}
                                </div>
                            </div>
                        </div>
                    </Card>
                }
            </div>
        </MainContent>
    </>
}
