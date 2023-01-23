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

export default function StoryIndicator({ length, active, pause, onTimeout }) {
  function getIndicatorItems() {
    const indicators = []
    const [position, setPosition] = useState(-100)

    useEffect(() => {
      // stop running if story done
      if (position >= 0 && active === length -1) {
        return
      }

      // reset position on timeout
      if (position >= 0) {
        setPosition(-100)
        onTimeout()
      }

      // prevent running on pause
      let timeout = null
      if (!pause) {
        timeout = setTimeout(() => {
          setPosition(position + 1);
        }, 50);
      }
  
      return () => {
        clearTimeout(timeout);
      };
    }, [position, pause]);

    for (let i = 0; i < length; i++) {
      indicators.push(
        <StoryIndicatorItem key={i} status={getIndicatorStatus(i, active)}>
          {i === active && <RunningIndicator style={{ transform: `translateX(${position}%)` }} />}
        </StoryIndicatorItem>
      )
    }

    return indicators
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