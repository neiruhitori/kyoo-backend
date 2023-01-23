import { useState } from "react"
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
    backgroundColor: '#0D1117',
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

export default function Story({ stories, timeoutDuration = TIMEOUT_PERSECONDS, onDone, style }) {
  if (!stories.length) throw new Error('Story item can\'t be empty')

  const [activeStoryIndex, setActiveStoryIndex] = useState(0)
  const [isPause, setIsPause] = useState(false)

  const activeStory = stories[activeStoryIndex]

  const handleStoryTimeout = () => {
    if (activeStoryIndex === stories.length - 1) return
    setActiveStoryIndex(activeStoryIndex + 1)
  }

  const handleMouseDown = () => setIsPause(true)

  const handleMouseUp = () => setIsPause(false)

  return <StoryContainer style={style}>
    <StoryHead>
      <StoryIndicator
        length={stories.length}
        active={activeStoryIndex}
        pause={isPause}
        onTimeout={handleStoryTimeout}
        onDone={onDone}
      />
    </StoryHead>

    {activeStory.type === storyType.TEXT && <TextStory
      text={activeStory.text}
      background={activeStory.color}
      fontSize={activeStory.font_size}
      onMouseDown={handleMouseDown}
      onMouseUp={handleMouseUp}
    />}

    {activeStory.type === storyType.IMAGE && <ImageStory
      title={activeStory.title}
      src={`/storage/${activeStory.image_url}`}
      caption={activeStory.caption}
      onMouseDown={handleMouseDown}
      onMouseUp={handleMouseUp}
    />}
  </StoryContainer>
}