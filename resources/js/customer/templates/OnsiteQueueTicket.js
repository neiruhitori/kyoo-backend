import  { forwardRef } from 'react'
import InfoAlert from '../components/InfoAlert'
import { format, formatBrowser } from '../utils/date'

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

            <div>
                <p style={{
                    color: '#A7A7A7',
                    marginBottom: '.6rem'
                }}>Tanggal Antri</p>
                <div style={{
                    fontSize: '1.2rem',
                    fontWeight: '700'
                }}>{format(formatBrowser(booking.date))}</div>
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

        <div style={{
            marginBottom: '1.7rem'
        }}>
            <p style={{
                color: '#A7A7A7',
                marginBottom: '.6rem'
            }}>Lokasi</p>
            <div style={{
                fontSize: '1.2rem',
                fontWeight: '700'
            }}>{branch.name}, {branch.address}</div>
        </div>

        <InfoAlert>
            <h4 style={{
                fontSize: '1rem',
                marginBottom: '.375rem',
                textTransform: 'capitalize'
            }}>Informasi Status Antrian</h4>

            <p style={{
                lineHeight: '1.5',
                fontSize: '1rem'
            }}>
                Akses ke alamat web <strong>scan.kyoo.id</strong>, pilih Kode Unik, masukan Kode Unik antrian anda untuk melihat status antrian
            </p>
        </InfoAlert>

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

            <div style={{
                height: '24px'
            }}>
                <img src="/img/logo-color.svg" alt="" style={{
                    height: '100%'
                }} />
            </div>
        </div>
    </div>
})