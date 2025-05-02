import styled from 'styled-components'

import Card from '../Card'
import AngleRightIcon from '../../icons/AngleRightIcon'
import { Children } from 'react'

const ServiceCard = styled.div(() => ({
    backgroundColor:'#bddbff',
    display: 'flex',
    justifyContent:'center',
    height:'450px',
    boxShadow: 'rgba(0, 0, 0, 0.1) 0px 7px 40px',
    borderRadius:' 0px 0px 12px 12px',
    padding: '1.125rem',
    transition: 'transform 0.5s ease-in-out',
    transform: 'translateY(0%)',
    zIndex: 1000
}))
const Header = styled.h5(() => ({
    marginBottom:'0.5rem', 
    marginTop:'0.5rem', 
    textAlign:'center', 
    fontSize:'1rem'
}))

function ServiceCardStyle3(props) {
    return <ServiceCard {...props}>
        <div style={{ width:'25rem', display:'flex', flexDirection:'column' }}>

            <Header>Slot Waktu</Header>
            <p style={{ color:'#838383', fontSize:'0.85rem', textAlign:'center', marginBottom:'1.5rem' }}>
                Pilih tanggal dan sesi waktu yang tersedia
            </p>
            {props.children}
        </div>
    </ServiceCard>
}

export default ServiceCardStyle3