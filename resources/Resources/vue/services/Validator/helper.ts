import CheckString from '~/services/CheckString'

const messages = {
  required: ':field is required',
  mimes: 'File is invalid',
  maxText: ':field not more than :max chars',
  minText: ':field not less than :min chars',
  maxNumeric: 'Numeric not more than :max chars',
  minNumeric: 'Numeric not less than :min chars',
  maxSpecial: 'Special char not more than :max chars',
  minSpecial: 'Special char not less than :min chars',
  maxUppercase: 'Uppercase not more than :max chars',
  minUppercase: 'Uppercase not less than :min chars',
  maxLowercase: 'Lowercase not more than :max chars',
  minLowercase: 'Lowercase not less than :min chars',
  passMatchWith: 'Password must be match with :field',
  maxNumber: ':field not more than :max',
  minNumber: ':field not less than :min',
  decimal: 'Decimals are only allowed :digit digits',
  maxItem: 'This field not more than :max items',
  minItem: 'This field not less than :min items',
  noSpace: 'Spaces are not allowed',
  noSpecial: 'Special char are not allowed',
}

const messagesDtl = {
  required: 'This field is required',
  mimes: 'File is invalid',
  maxText: 'This field not more than :max chars',
  minText: 'This field not less than :min chars',
  maxNumeric: 'Number not more than :max chars',
  minNumeric: 'Number not less than :min chars',
  maxSpecial: 'Special char not more than :max chars',
  minSpecial: 'Special char not less than :min chars',
  maxUppercase: 'Uppercase not more than :max chars',
  minUppercase: 'Uppercase not less than :min chars',
  maxLowercase: 'Lowercase not more than :max chars',
  minLowercase: 'Lowercase not less than :min chars',
  maxNumber: 'This field not more than :max',
  minNumber: 'This field not less than :min',
  maxItem: 'This field not more than :max items',
  minItem: 'This field not less than :min items',
  maxLoc: 'This field not more than :max locations',
  minLoc: 'This field not less than :min locations',
  ltColumn: 'No greater than or equal to of :column',
  lteColumn: 'No greater than of :column',
  gtColumn: 'No less than or equal to of :column',
  gteColumn: 'No less than of :column',
}

const create = {
  errors: (forms, key) => {
    if (!forms[key].errors) {
      forms[key] = {
        ...forms[key],
        errors: [],
      }
    }
  },
  messages: (forms, key, rule) => {
    let message
    if (rule.includes('required')) {
      message = messages.required.replace(':field', forms[key].options.label)
    } else if (
      ['textfield', 'passwordfield', 'textareafield'].includes(
        forms[key].type,
      ) &&
      rule.includes('max:')
    ) {
      const split = rule.split(':')
      const max = split[1]
      message = messages.maxText
        .replace(':field', forms[key].options.label)
        .replace(':max', max)
    } else if (
      ['textfield', 'passwordfield', 'textareafield'].includes(
        forms[key].type,
      ) &&
      rule.includes('min:')
    ) {
      const split = rule.split(':')
      const min = split[1]
      message = messages.minText
        .replace(':field', forms[key].options.label)
        .replace(':min', min)
    } else if (
      ['popupfield', 'selectfield'].includes(forms[key].type) &&
      rule.includes('max:')
    ) {
      const split = rule.split(':')
      const max = split[1]
      message = messages.maxItem
        .replace(':field', forms[key].options.label)
        .replace(':max', max)
    } else if (
      ['popupfield', 'selectfield'].includes(forms[key].type) &&
      rule.includes('min:')
    ) {
      const split = rule.split(':')
      const min = split[1]
      message = messages.minItem
        .replace(':field', forms[key].options.label)
        .replace(':min', min)
    } else if (forms[key].type === 'numberfield' && rule.includes('max:')) {
      const split = rule.split(':')
      const max = split[1]
      message = messages.maxNumber
        .replace(':field', forms[key].options.label)
        .replace(':max', max)
    } else if (forms[key].type === 'numberfield' && rule.includes('min:')) {
      const split = rule.split(':')
      const min = split[1]
      message = messages.minNumber
        .replace(':field', forms[key].options.label)
        .replace(':min', min)
    } else if (
      forms[key].type === 'passwordfield' &&
      rule.includes('max_number:')
    ) {
      const split = rule.split(':')
      const max = split[1]
      message = messages.maxNumeric.replace(':max', max)
    } else if (
      forms[key].type === 'passwordfield' &&
      rule.includes('min_number:')
    ) {
      const split = rule.split(':')
      const min = split[1]
      message = messages.minNumeric.replace(':min', min)
    } else if (
      forms[key].type === 'passwordfield' &&
      rule.includes('max_special:')
    ) {
      const split = rule.split(':')
      const max = split[1]
      message = messages.maxSpecial.replace(':max', max)
    } else if (
      forms[key].type === 'passwordfield' &&
      rule.includes('min_special:')
    ) {
      const split = rule.split(':')
      const min = split[1]
      message = messages.minSpecial.replace(':min', min)
    } else if (
      forms[key].type === 'passwordfield' &&
      rule.includes('max_uppercase:')
    ) {
      const split = rule.split(':')
      const max = split[1]
      message = messages.maxUppercase.replace(':max', max)
    } else if (
      forms[key].type === 'passwordfield' &&
      rule.includes('min_uppercase:')
    ) {
      const split = rule.split(':')
      const min = split[1]
      message = messages.minUppercase.replace(':min', min)
    } else if (
      forms[key].type === 'passwordfield' &&
      rule.includes('max_lowercase:')
    ) {
      const split = rule.split(':')
      const max = split[1]
      message = messages.maxLowercase.replace(':max', max)
    } else if (
      forms[key].type === 'passwordfield' &&
      rule.includes('min_lowercase:')
    ) {
      const split = rule.split(':')
      const min = split[1]
      message = messages.minLowercase.replace(':min', min)
    } else if (forms[key].type === 'passwordfield' && rule.includes('match:')) {
      const split = rule.split(':')
      const field = split[1]
      message = messages.passMatchWith.replace(':field', field)
    } else if (forms[key].type === 'textfield' && rule.includes('no_space')) {
      message = messages.noSpace
    } else if (forms[key].type === 'textfield' && rule.includes('no_special')) {
      message = messages.noSpecial
    } else if (forms[key].type === 'numberfield' && rule.includes('decimal')) {
      const split = rule.split(':')
      const digit = split[1]
      message = messages.decimal
        .replace(':field', forms[key].options.label)
        .replace(':digit', digit)
    } else {
      message = messages[rule]
    }

    if (!forms[key].errors.includes(message)) {
      forms[key].errors.push(message)
    }
  },
}

const remove = {
  messages: (forms, key, rule) => {
    if (
      forms[key].errors &&
      forms[key].errors.length > 0 &&
      forms[key].errors.includes(messages[rule])
    ) {
      forms[key].errors = forms[key].errors.filter((dt) => {
        if (dt !== messages[rule]) {
          return true
        }
      })
    }
  },
}

export { messages, messagesDtl, create, remove }
