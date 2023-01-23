export function stringToHTML(str) {
  const stringToLink = match => {
    const hasPrefix = /(https?:\/\/)/.test(match)
    return `<a href="${hasPrefix ? match : 'http://' + match}" target="__blank">${match}</a>`
  }
  const stringToBreakLine = () => '<br>'

  const linkRegex = /(https?:\/\/)?(www\.)?([a-zA-Z0-9]+\.[a-zA-Z]{2,3}(\.[a-z]{2})?)/g
  const breakLineRegex = /(?:\r\n|\r|\n)/g

  return str
    .replace(linkRegex, stringToLink)
    .replace(breakLineRegex, stringToBreakLine)
}