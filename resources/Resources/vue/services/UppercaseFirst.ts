export default (str: string): string => {
  const firstLetter = str.substr(0, 1)
  return firstLetter.toUpperCase() + str.substr(1)
};