import Card from './Card'
import SkeletonItem from './SkeletonItem'

function ServiceCardSkeleton(props) {
    return <Card style={{
        height: '72px',
        ...props.style
    }}>
        <SkeletonItem />

        <SkeletonItem height="0.75rem" width="150px" style={{
            marginTop: '0.75rem'
        }} />
    </Card>
}

export default ServiceCardSkeleton