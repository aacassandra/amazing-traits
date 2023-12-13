export default (no_hex = false) => {
  const randomColor = Math.floor(Math.random() * 16777215).toString(16)
  if (no_hex) {
    return randomColor
  } else {
    return '#' + randomColor
  }
}
