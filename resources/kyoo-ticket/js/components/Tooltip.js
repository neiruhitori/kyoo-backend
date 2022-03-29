import styled from 'styled-components'

const TooltipWrapper = styled.div`
    border-radius: 12px;
    background-color: #252A31;
    color: #FFFFFF;
`

const TooltipRoot = styled.div`
    background-color: rgba(0, 0, 0, 0.5);
    position: fixed;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    display: flex;
    justify-content: center;
    align-items: end;
    z-index: 9999;
`

function Tooltip({ children }) {
    return <TooltipRoot>
        <div style={{
            maxWidth: '420px',
            padding: '0 1.375rem 2rem 1.375rem'
        }}>
            <TooltipWrapper>
                {children}
            </TooltipWrapper>
        </div>
    </TooltipRoot>
}

export default Tooltip