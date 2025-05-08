import styled from 'styled-components'

import Card from '../Card'
import SkeletonItem from '../SkeletonItem'
import BlueCard from '../BlueCard'

function ServiceItemSkeletonStyle1() {
    return <Card style={{
        height: '85px',
        display: 'flex',
        backgroundImage: 'linear-gradient(270deg, #103C7C -24.91%, #2F5B9B 118.64%)',
    }}>
        <div style={{
            flex: '1 1 0%',
        }}>
            <SkeletonItem width="165px"/>
            <SkeletonItem height="0.75rem" width="120px" style={{
                marginTop: '1rem'
            }} />
        </div>
        <div style={{
            boxShadow: '0px 7px 40px rgba(0, 0, 0, 0.1)',
            borderRadius: '12px',
            padding: '1.125rem',
            width: '164px',
            boxShadow: 'none',
            margin: '-1.125rem -1.125rem -1.125rem 0'
        }}>
            <SkeletonItem width="90px" height="0.75rem" style={{
                marginBottom: '0.75rem'
            }} />
            <SkeletonItem width="35px" height="1.75rem" />
        </div>
    </Card>
}

export default ServiceItemSkeletonStyle1