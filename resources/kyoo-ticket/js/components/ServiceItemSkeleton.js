import styled from 'styled-components'

import Card from './Card'
import SkeletonItem from './SkeletonItem'
import BlueCard from './BlueCard'

function ServiceItemSkeleton() {
    return <Card style={{
        height: '85px',
        display: 'flex'
    }}>
        <div style={{
            flex: '1 1 0%',
        }}>
            <SkeletonItem />
            <SkeletonItem height="0.75rem" width="80px" style={{
                marginTop: '0.75rem'
            }} />
        </div>
        <BlueCard style={{
            width: '164px',
            margin: '-1.125rem -1.125rem -1.125rem 0'
        }}>
            <SkeletonItem width="90px" height="0.75rem" style={{
                marginBottom: '0.75rem'
            }} />
            <SkeletonItem width="35px" height="1.75rem" />
        </BlueCard>
    </Card>
}

export default ServiceItemSkeleton