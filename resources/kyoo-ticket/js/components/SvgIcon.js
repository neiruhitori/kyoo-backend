import styled from 'styled-components'

const SvgIconRoot = styled.svg.attrs((props) => ({
    xmlns: 'http://www.w3.org/2000/svg',
    viewBox: props.viewBox ||'0 0 512 512'
}))(props => {
    return {
        height: props.height || props.style?.height || '1.125rem',
        width: props.width || props.style?.width || 'auto',
        fill: props.fill || props.color || '#000000'
    }
})

function SvgIcon(props) {
    return <SvgIconRoot {...props}>
        {props.children}
    </SvgIconRoot>
}

export default SvgIcon