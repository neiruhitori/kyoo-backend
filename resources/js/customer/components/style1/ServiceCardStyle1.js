import styled from 'styled-components'

import Card from '../Card'
import AngleRightIcon from '../../icons/AngleRightIcon'
import { Children } from 'react'
import useLocalization from './../../hooks/useLocalization';

const ServiceCard = styled.div(() => ({
    backgroundColor:' rgba(74, 144, 226, 0.25)',
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

function ServiceCardStyle1(props) {
    const {t, locale} = useLocalization();
    return <ServiceCard {...props}>
        <div style={{ width:'25rem', display:'flex', flexDirection:'column' }}>

            <Header>{t('Time Slot')}</Header>
            <p style={{ color:'#838383', fontSize:'0.85rem', textAlign:'center', marginBottom:'1.5rem' }}>
                {t('Choose an available date and time session')}
            </p>
            {props.children}
        </div>
    </ServiceCard>
}

export default ServiceCardStyle1