import styled from "styled-components"
import { stringToHTML } from "../utils/string"

const TextStoryContainer = styled.div(({ background, fontSize }) => {
  return {
    display: 'flex',
    flexDirection: 'column',
    alignItems: 'center',
    justifyContent: 'center',
    overflow: 'hidden',
    height: '100%',
    width: '100%',
    backgroundColor: background,
    fontSize,
    color: '#FFFFFF',
    padding: '1em'
  }
})

const TextStoryWrapper = styled.p(() => {
  return {
    textAlign: 'center',
    margin: '0',
    overflowWrap: 'anywhere',
    overflow: 'hidden'
  }
})

export default function TextStory({ text, background, fontSize, onMouseDown, onMouseUp }) {
  return <TextStoryContainer
    background={background}
    fontSize={fontSize}
    onMouseDown={onMouseDown}
    onMouseUp={onMouseUp}
  >
    <TextStoryWrapper dangerouslySetInnerHTML={{ __html: stringToHTML(text) }} />

    <style scoped>
      {`
        a {
          display: inline-block;
          background-color: rgba(255, 255, 255, .2);
          color: #FFFFFF;
          padding: 0 .3em;
          border-radius: .25em;
        }
      `}
    </style>
  </TextStoryContainer>
}