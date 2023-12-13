export default (t: any, path: string) => {
  if (t) {
    const value = path.split('.').reduce((r, k) => r?.[k], t)
    if (value !== undefined) {
      return value
    } else if (t[path]) {
      return t[path]
    } else {
      return undefined
    }
  }
}
