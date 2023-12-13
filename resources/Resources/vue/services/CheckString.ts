export default (
  str: string,
  get: 'uppercase' | 'lowercase' | 'number' | 'special' | 'space',
) => {
  let upper = 0
  let lower = 0
  let number = 0
  let special = 0
  const space = str.split(' ').length - 1
  for (let i = 0; i < str.length; i++) {
    if (str[i] >= 'A' && str[i] <= 'Z') upper++
    else if (str[i] >= 'a' && str[i] <= 'z') lower++
    else if (str[i] >= '0' && str[i] <= '9') number++
    else if (str[i] != '_') special++
  }

  if (get === 'uppercase') {
    return upper
  } else if (get === 'lowercase') {
    return lower
  } else if (get === 'number') {
    return number
  } else if (get === 'special') {
    return special
  } else if (get === 'space') {
    return space
  }
}
