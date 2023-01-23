import { useState } from "react"
import styled from "styled-components"

import { stringToHTML } from "../utils/string"

const ImageStoryContainer = styled.div(() => {
  return {
    display: 'flex',
    flexDirection: 'column',
    alignItems: 'center',
    justifyContent: 'center',
    overflow: 'hidden',
    height: '100%',
    width: '100%',
    backgroundColor: '#0D1117',
    color: '#FFFFFF',
    fontSize: '14px'
  }
})

const ImageContent = styled.img(() => {
  return {
    width: '100%',
    height: '100%',
    objectFit: 'contain'
  }
})

const CaptionContent = styled.p(() => {
  return {
    position: 'absolute',
    left: '0',
    right: '0',
    bottom: '0',
    zIndex: '1',
    margin: '0',
    padding: '2rem 1.375rem',
    textAlign: 'center',
    backgroundColor: 'rgba(0, 0, 0, .6)',
    cursor: 'pointer'
  }
})

export default function ImageStory({ title, src, caption, onMouseUp, onMouseDown }) {
  const [isExpanded, setIsExpanded] = useState(false)

  const toggleText = () => setIsExpanded(!isExpanded)

  const fullText = stringToHTML(caption)

  let captionComponent = <CaptionContent onClick={toggleText} dangerouslySetInnerHTML={
    {__html: fullText }
  } />

  if (caption.length > 120) {
    const shortText = stringToHTML(caption.substr(0, 120)) + `... <strong>Baca selengkapnya</strong>`

    captionComponent = <CaptionContent onClick={toggleText} dangerouslySetInnerHTML={
      {__html: isExpanded ? fullText : shortText }
    } />
  }

  return <ImageStoryContainer>
    <ImageContent
      src={src}
      alt={title}
      onMouseUp={onMouseUp}
      onMouseDown={onMouseDown}
    />

    {captionComponent}

    <style>
      {`
        a {
          color: #FFFFFF;
          text-decoration: underline; 
        }

        a.read-more {
          font-weight: bold;
          text-decoration: none;
        }
      `}
    </style>
  </ImageStoryContainer>
}