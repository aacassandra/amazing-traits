export default {
  getCurrentYear: () => {
    return new Date().getFullYear()
  },
  getCurrentMonth: (long = false) => {
    if (!long) {
      // return 0, 1, 2, 3, 4 - 11
      return new Date().getMonth()
    } else {
      // return January, ....
      const today = new Date()
      return today.toLocaleString('default', { month: 'long' })
    }
  },
}
