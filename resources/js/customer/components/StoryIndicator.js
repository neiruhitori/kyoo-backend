import { useEffect, useState } from "react"
import styled from "styled-components"

const indicatorStatus = {
  SEEN: 'seen',
  UNSEEN: 'unseen',
  ACTIVE: 'active'
}

const StoryIndicatorContainer = styled.div(() => {
  return {
    display: 'flex',
    height: '7px'
  }
})

const StoryIndicatorItem = styled.div(({ status }) => {
  const bgStatusMap = {
    [indicatorStatus.SEEN]: '#FFFFFF',
    [indicatorStatus.UNSEEN]: 'rgba(255, 255, 255, .3)',
    [indicatorStatus.ACTIVE]: 'rgba(255, 255, 255, .3)'
  }

  return {
    flex: '1',
    height: '100%',
    borderRadius: '3.5px',
    backgroundColor: bgStatusMap[status],
    margin: '0 2px',
    overflow: 'hidden',
    position: 'relative'
  }
})

const RunningIndicator = styled.div(() => {
  return {
    position: 'absolute',
    top: '0',
    left: '0',
    backgroundColor: '#FFFFFF',
    height: '100%',
    width: '100%'
  }
})

export default function StoryIndicator({
  length,
  active,
  currentProgress
}) {
  function getIndicatorItems() {
    const indicators = []

    for (let i = 0; i < length; i++) {
      indicators.push(
        <StoryIndicatorItem key={i} status={getIndicatorStatus(i, active)}>
          {i === active && <RunningIndicator
            style={{ transform: `translateX(${progressToTranslateX(currentProgress)}%)` }}
          />}
        </StoryIndicatorItem>
      )
    }

    return indicators
  }

  function progressToTranslateX(progress) {
    return progress - 100
  }

  function getIndicatorStatus(currentIndicator, activeIndicator) {
    if (currentIndicator < activeIndicator) {
      return indicatorStatus.SEEN
    }

    if (currentIndicator === activeIndicator) {
      return indicatorStatus.ACTIVE
    }

    return indicatorStatus.UNSEEN
  }

  return <StoryIndicatorContainer> 
    {getIndicatorItems()}
  </StoryIndicatorContainer>
}