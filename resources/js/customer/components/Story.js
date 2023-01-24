import { useEffect, useState } from "react"
import styled from "styled-components"

import StoryIndicator from "./StoryIndicator"
import TextStory from "./TextStory"
import ImageStory from "./ImageStory"

const TIMEOUT_PERSECONDS = 5

const storyType = {
  IMAGE: 'image',
  TEXT: 'text'
}

const StoryContainer = styled.div(() => {
  return {
    position: 'relative',
    backgroundColor: '#000000',
    height: '100vh',
    fontSize: '22px',
    lineHeight: '1.5'
  }
})

const StoryHead = styled.div(() => {
  return {
    position: 'absolute',
    top: '0',
    left: '0',
    right: '0',
    zIndex: '2',
    backgroundImage: 'linear-gradient(rgba(11, 20, 26, .65), rgba(11, 20, 26, 0))',
    padding: '2rem 1.375rem'
  }
})

const StoryTool = styled.div(() => {
  return {
    display: 'flex',
    justifyContent: 'space-between',
    alignItems: 'center',
    padding: '1rem 0'
  }
})

const StoryButton = styled.button(() => {
  return {
    backgroundColor: 'rgba(0, 0, 0, .6)',
    color: '#FFFFFF',
    border: '1px solid #FFFFFF',
    borderRadius: '6px',
    padding: '.5rem 1rem'
  }
})

export default function Story({ stories, timeoutDuration = TIMEOUT_PERSECONDS, onDone, style }) {
  if (!stories.length) throw new Error('Story item can\'t be empty')

  const [activeIndex, setActiveIndex] = useState(0)
  const [isPause, setIsPause] = useState(false)
  const [progress, setProgress] = useState(0)

  const activeStory = stories[activeIndex]

  useEffect(() => {
    if (progress >= 100) {
      handleTimeout()
      return
    }

    if (!isPause) {
      const progressIncTimeout = setTimeout(() => {
        setProgress(progress + 1)
      }, 50)
  
      return () => clearTimeout(progressIncTimeout)
    }
  }, [progress, isPause])

  function handlePause() {
    setIsPause(true)
  }

  function handleResume() {
    setIsPause(false)
  }

  function handleTimeout() {
    if (activeIndex === stories.length - 1) return onDone()
    setProgress(0)
    setActiveIndex(activeIndex + 1)
  }

  function handleNext() {
    if (activeIndex === stories.length - 1) return onDone()
    setProgress(0)
    setActiveIndex(activeIndex + 1)
  }

  function handlePrev() {
    if (activeIndex === 0) return
    setProgress(0)
    setActiveIndex(activeIndex - 1)
  }

  return <StoryContainer style={style}>
    <StoryHead>
      <StoryIndicator
        length={stories.length}
        active={activeIndex}
        pause={isPause}
        currentProgress={progress}
      />

      <StoryTool>
        <StoryButton onClick={handlePrev}>
          Prev
        </StoryButton>

        <StoryButton onClick={handleNext}>
          Next
        </StoryButton>
      </StoryTool>
    </StoryHead>

    {activeStory.type === storyType.TEXT && <TextStory
      text={activeStory.text}
      background={activeStory.color}
      fontSize={activeStory.font_size}
      onMouseDown={handlePause}
      onMouseUp={handleResume}
    />}

    {activeStory.type === storyType.IMAGE && <ImageStory
      title={activeStory.title}
      src={`/storage/${activeStory.image_url}`}
      caption={activeStory.caption}
      onMouseDown={handlePause}
      onMouseUp={handleResume}
    />}
  </StoryContainer>
}