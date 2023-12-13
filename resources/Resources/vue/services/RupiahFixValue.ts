import { RupiahToInt } from '~/services/index'

export default (targetValue: any) => {
  let fixValue = null
  if (
    typeof targetValue === 'string' &&
    targetValue.includes('Rp. ')
  ) {
    fixValue = RupiahToInt(targetValue)
  } else {
    if (typeof targetValue === 'number') {
      fixValue = targetValue
    } else if (typeof targetValue === 'string') {
      if (targetValue) {
        if (targetValue.indexOf('.')) {
          fixValue = parseFloat(targetValue)
        } else {
          fixValue = parseInt(targetValue)
        }
      } else {
        fixValue = 0
      }
    }
  }

  return fixValue
}