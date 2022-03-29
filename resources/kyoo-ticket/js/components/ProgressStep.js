import styled from 'styled-components'

const ProgressStepRoot = styled.div`
    width: 100%;
    display: flex;
    align-items: center;
`

const StepItem = styled.div`
    flex: 1;
    display: flex;
    align-items: center;

    &:after {
        content: '';
        display: inline-block;
        width: 0.75rem;
        height: 0.75rem;
        background-color: #DCE3E9;
        border-radius: 100%;
        z-index: 1;
    }

    &:before {
        content: '';
        display: inline-block;
        height: 0.375rem;
        flex: 1 1 0%;
        background-color: #DCE3E9;
        margin: 0 -0.1rem;
        z-index: -1;
    }

    &:first-child {
        width: 0.75rem;
        flex-grow: 0;

        &:before {
            content: none;
        }
    }
`

const ActiveStepItem = styled(StepItem)`
    &:after {
        background-color: #252A31;
    }

    &:before {
        background-color: #252A31;
    }
`

function ProgressStep(props) {
    const stepItems = []

    for (let i = 0; i < props.total; i++) {
        if (i <= props.active) {
            stepItems.push(<ActiveStepItem key={i} />)
        } else {
            stepItems.push(<StepItem key={i} />)
        }
    }

    return <ProgressStepRoot>
        {stepItems}
    </ProgressStepRoot>
}

export default ProgressStep