import  { forwardRef } from 'react'
import InfoAlert from '../components/InfoAlert'
import { formatBrowser, format, formatTime } from '../utils/date'
import QRCode from 'react-qr-code';

export default forwardRef(function OnsiteQueueTicket({ booking, branch, style }, ref) {
    return <div ref={ref} style={{
        padding: '1.7rem',
        backgroundColor: '#FFFFFF',
        ...style
    }}>
        {!!branch.logo && <div style={{
            textAlign: 'center',
            marginBottom: '1.7rem',
            height: '4.5rem'
        }}>
            <img src={`/storage/${branch.logo}`} alt="" style={{
                height: '100%'
            }} />
        </div>}

        <div style={{
            textAlign: 'center',
            marginBottom: '1rem'
        }}>
            <QRCode value={booking.booking_code.toUpperCase() || ''} size={100}/>
        </div>

        <div style={{
            textAlign: 'center'
        }}>
            <p style={{
                color: '#7A7A7A',
                marginBottom: '.325rem'
            }}>{booking.queue_no ? 'Nomor Antrian' : 'Kode Booking' }</p>
            <h1 style={{
                fontSize: '3.5rem',
                textTransform: 'uppercase'
            }}>{booking.queue_no ?? booking.booking_code}</h1>
        </div>

        <div style={{
            borderTop: '1px dashed #7A7A7A',
            margin: '1.7rem 0'
        }}></div>

        <div style={{
            display: 'grid',
            gridTemplateColumns: '1fr 1fr',
            gap: '1.5rem'
        }}>
            {booking.queue_no &&
                <div>
                    <p style={{ color: '#7A7A7A',
                        marginBottom: '.6rem'
                    }}>Kode Unik</p>
                    <div style={{
                        fontSize: '1.2rem'
                    }}>{booking.booking_code.toUpperCase()}</div>
                </div>
            }

            <div style={{
                textAlign: booking.queue_no ? 'right' : ''
            }}>
                <p style={{
                    color: '#7A7A7A',
                    marginBottom: '.6rem'
                }}>Layanan</p>
                <div style={{
                    fontSize: '1.2rem'
                }}>{booking.service_name}</div>
            </div>

            <div style={{
                textAlign: booking.queue_no ? '' : 'right'
            }}>
                <p style={{
                    color: '#7A7A7A',
                    marginBottom: '.6rem'
                }}>Tanggal Antri</p>
                <div style={{
                    fontSize: '1.2rem'
                }}>{format(formatBrowser(booking.date))}</div>
            </div>

            {booking.queue_no &&
                <div style={{
                    textAlign: 'right'
                }}>
                    <p style={{
                        color: '#7A7A7A',
                        marginBottom: '.6rem'
                    }}>Waktu</p>
                    <div style={{
                        fontSize: '1.2rem'
                    }}>{formatTime(formatBrowser(booking.date))}</div>
                </div>
            }
        </div>

        <div style={{
            borderTop: '1px dashed #7A7A7A',
            margin: '1.7rem 0'
        }}></div>

        {(booking.name || booking.email || booking.phone)  && <>
            <div style={{
                display: 'grid',
                gridTemplateColumns: '1fr 1fr',
                gap: '1.5rem'
            }}>
                {!!booking.name && <div>
                    <p style={{ color: '#7A7A7A',
                        marginBottom: '.6rem'
                    }}>Nama Pengunjung</p>
                    <div style={{
                        fontSize: '1.2rem'
                    }}>{booking.name}</div>
                </div>}

                {!!booking.phone && <div style={{
                    textAlign: 'right'
                }}>
                    <p style={{
                        color: '#7A7A7A',
                        marginBottom: '.6rem'
                    }}>No. Handphone</p>
                    <div style={{
                        fontSize: '1.2rem'
                    }}>{booking.phone}</div>
                </div>}

                {!!booking.email && <div>
                    <p style={{ color: '#7A7A7A',
                        marginBottom: '.6rem'
                    }}>Email</p>
                    <div style={{
                        fontSize: '1.2rem'
                    }}>{booking.email}</div>
                </div>}
            </div>

            <div style={{
                borderTop: '1px dashed #7A7A7A',
                margin: '1.7rem 0'
            }}></div>
        </>}

        <div style={{
            marginBottom: '2.25rem'
        }}>
            <p style={{
                color: '#7A7A7A',
                marginBottom: '.6rem'
            }}>Lokasi</p>
            <div style={{
                fontSize: '1.2rem'
            }}>
                {branch.name}
                <p style={{
                    marginTop: '.6rem'
                }}>{branch.address}</p>
            </div>
        </div>

        <InfoAlert>
            <h4 style={{
                fontSize: '1rem',
                marginBottom: '.375rem',
                textTransform: 'capitalize'
            }}>Informasi Status Antrian</h4>

            {booking.queue_no ?
                <p style={{
                    lineHeight: '1.5',
                    fontSize: '1rem'
                }}>
                    Akses ke alamat web <strong>scan.kyoo.id</strong>, pilih Kode Unik, masukan Kode Unik antrian anda untuk melihat status antrian
                </p>
                :
                <p style={{
                    lineHeight: '1.5',
                    fontSize: '1rem'
                }}>
                    Simpan kode ini dan masukkan di mesin Web Kiosk di tempat pelayanan untuk mendapatkan nomer antrian
                </p>
            }
        </InfoAlert>

        <div style={{
            display: 'flex',
            alignItems: 'center',
            justifyContent: 'center',
            marginTop: '4.5rem'
        }}>
            <div style={{
                color: '#7A7A7A',
                marginRight: '.625rem'
            }}>Powered by</div>

            <div style={{
                height: '20px'
            }}>
                <img src="/img/kyoo-logo.png" alt="" style={{
                    height: '100%'
                }} />
            </div>
        </div>
    </div>
})
