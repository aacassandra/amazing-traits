import PhoneFormatter from 'phone-formatter'

export default (value, output, countryCode, replaceCountryCode = true) => {
  if (value) {
    let newVal = value.replace(/[^0-9]+/g, '')
    if (newVal.charAt(0) == 0 && replaceCountryCode) {
      newVal = newVal.replace(newVal.charAt(0), countryCode)
    }

    if (newVal) {
      return PhoneFormatter.format(newVal, output, {
        normalize: true,
      }).replace(/N/g, '')
    } else {
      return ''
    }
  } else {
    return ''
  }
}
