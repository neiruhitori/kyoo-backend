import  { forwardRef } from 'react'
import { format } from '../utils/date'

export default forwardRef(function OnsiteQueueTicket({ booking, branch, style }, ref) {
    return <div ref={ref} style={{
        padding: '1.7rem',
        backgroundColor: '#FFFFFF',
        ...style
    }}>
        <div style={{
            display: 'flex',
            alignItems: 'center',
            marginBottom: '1.7rem'
        }}>
            <div style={{
                flex: '1'
            }}>
                <img src={`/storage/${branch.logo}`} alt="" style={{
                    height: '24px'
                }} />
            </div>
            
            <p style={{
                textAlign: 'right',
                flex: '1'
            }}>{format(new Date(booking.date))}</p>
        </div>

        <div style={{
            textAlign: 'center'
        }}>
            <p style={{
                color: '#A7A7A7',
                marginBottom: '.325rem'
            }}>Nomor Antrian</p>
            <h1 style={{
                fontSize: '3.5rem'
            }}>{booking.queue_no}</h1>
        </div>

        <div style={{
            borderTop: '1px dashed #A7A7A7',
            margin: '1.7rem 0'
        }}></div>

        <div style={{
            display: 'grid',
            gridTemplateColumns: '1fr 1fr',
            gap: '1.2rem'
        }}>
            <div>
                <p style={{ color: '#A7A7A7',
                    marginBottom: '.6rem'
                }}>Kode Unik</p>
                <div style={{
                    fontSize: '1.2rem',
                    fontWeight: '700'
                }}>{booking.booking_code.toUpperCase()}</div>
            </div>

            <div style={{
                textAlign: 'right'
            }}>
                <p style={{
                    color: '#A7A7A7',
                    marginBottom: '.6rem'
                }}>Layanan</p>
                <div style={{
                    fontSize: '1.2rem',
                    fontWeight: '700'
                }}>{booking.service_name}</div>
            </div>
        </div>

        <div style={{
            borderTop: '1px dashed #A7A7A7',
            margin: '1.7rem 0'
        }}></div>

        {(booking.name || booking.email || booking.phone)  && <>
            <div style={{
                display: 'grid',
                gridTemplateColumns: '1fr 1fr',
                gap: '1.2rem'
            }}>
                {!!booking.name && <div>
                    <p style={{ color: '#A7A7A7',
                        marginBottom: '.6rem'
                    }}>Nama Pengunjung</p>
                    <div style={{
                        fontSize: '1.2rem',
                        fontWeight: '700'
                    }}>{booking.name}</div>
                </div>}

                {!!booking.phone && <div style={{
                    textAlign: 'right'
                }}>
                    <p style={{
                        color: '#A7A7A7',
                        marginBottom: '.6rem'
                    }}>No. Handphone</p>
                    <div style={{
                        fontSize: '1.2rem',
                        fontWeight: '700'
                    }}>{booking.phone}</div>
                </div>}

                {!!booking.email && <div>
                    <p style={{ color: '#A7A7A7',
                        marginBottom: '.6rem'
                    }}>Email</p>
                    <div style={{
                        fontSize: '1.2rem',
                        fontWeight: '700'
                    }}>{booking.email}</div>
                </div>}
            </div>

            <div style={{
                borderTop: '1px dashed #A7A7A7',
                margin: '1.7rem 0'
            }}></div>
        </>}

        <div>
            <p style={{
                color: '#A7A7A7',
                marginBottom: '.6rem'
            }}>Lokasi</p>
            <div style={{
                fontSize: '1.2rem',
                fontWeight: '700'
            }}>{branch.name}, {branch.address}</div>
        </div>

        <div style={{
            display: 'flex',
            alignItems: 'center',
            justifyContent: 'center',
            marginTop: '4.5rem'
        }}>
            <div style={{
                color: '#A7A7A7',
                marginRight: '.5rem'
            }}>Powered by</div>

            <div>
                <img src="/img/logo-color.svg" alt="" style={{
                    height: '24px'
                }} />
            </div>
        </div>
    </div>
})