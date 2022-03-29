import styled from 'styled-components'

const SkeletonItem = styled.div`
    background-color: ${props => props.color || '#D6D6D6'};
    border-radius: 4px;
    height: ${props => props.height || '1rem'};
    width: ${props => props.width || '50px'};
`

export default SkeletonItem