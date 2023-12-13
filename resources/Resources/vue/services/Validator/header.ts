/* eslint-disable @typescript-eslint/ban-ts-comment */
import { create, remove } from './helper'
import { FormHeader } from '~/types/form/header'
import CheckString from '~/services/CheckString'

export default (form: FormHeader, mode: 'create' | 'edit' | 'preview' | '') => {
  let someErrors = 0
  Object.entries(form.properties).forEach(([key]) => {
    if (key !== 'errors') {
      const check = (mode: 'create' | 'edit' | 'preview' | '') => {
        if (
          form.properties[key].rules &&
          form.properties[key].rules.length > 0
        ) {
          form.properties[key].rules.forEach((rule) => {
            rule = rule.toLowerCase()
            if (rule.includes('required')) {
              // @ts-ignore
              if (
                (form.properties[key].type === 'selectfield' &&
                  form.properties[key].options.multiple) ||
                form.properties[key].type === 'tagsfield'
              ) {
                // @ts-ignore
                if (!form.properties[key].value.length) {
                  someErrors++
                  create.errors(form.properties, key)
                  create.messages(form.properties, key, rule)
                } else {
                  create.errors(form.properties, key)
                  create.messages(form.properties, key, rule)
                }
              } else if (form.properties[key].type === 'numberfield') {
                if (
                  form.properties[key].value === null ||
                  form.properties[key].value === undefined ||
                  form.properties[key].value === ''
                ) {
                  someErrors++
                  create.errors(form.properties, key)
                  create.messages(form.properties, key, rule)
                } else {
                  create.errors(form.properties, key)
                  create.messages(form.properties, key, rule)
                }
              } else if (form.properties[key].type === 'filefield') {
                if (mode === 'edit') {
                  if (
                    !form.properties[key].value &&
                    form.properties[key].hasChanged
                  ) {
                    someErrors++
                    create.errors(form.properties, key)
                    create.messages(form.properties, key, rule)
                  } else {
                    create.errors(form.properties, key)
                    create.messages(form.properties, key, rule)
                  }
                } else if (mode === 'create') {
                  if (!form.properties[key].value) {
                    someErrors++
                    create.errors(form.properties, key)
                    create.messages(form.properties, key, rule)
                  } else {
                    create.errors(form.properties, key)
                    create.messages(form.properties, key, rule)
                  }
                }
              } else {
                if (!form.properties[key].value) {
                  someErrors++
                  create.errors(form.properties, key)
                  create.messages(form.properties, key, rule)
                } else {
                  create.errors(form.properties, key)
                  create.messages(form.properties, key, rule)
                }
              }
            } else if (
              ['textfield', 'passwordfield', 'textareafield'].includes(
                form.properties[key].type,
              ) &&
              rule.includes('max:')
            ) {
              const split = rule.split(':')
              const max = parseInt(split[1])
              // @ts-ignore
              if (form.properties[key].value.length > max) {
                someErrors++
                create.errors(form.properties, key)
                create.messages(form.properties, key, rule)
              } else {
                create.errors(form.properties, key)
                create.messages(form.properties, key, rule)
              }
            } else if (
              ['textfield', 'passwordfield', 'textareafield'].includes(
                form.properties[key].type,
              ) &&
              rule.includes('min:')
            ) {
              const split = rule.split(':')
              const min = parseInt(split[1])
              // @ts-ignore
              if (form.properties[key].value.length < min) {
                someErrors++
                create.errors(form.properties, key)
                create.messages(form.properties, key, rule)
              } else {
                create.errors(form.properties, key)
                create.messages(form.properties, key, rule)
              }
            } else if (
              ['popupfield', 'selectfield'].includes(
                form.properties[key].type,
              ) &&
              form.properties[key].options.multiple &&
              rule.includes('min:')
            ) {
              const split = rule.split(':')
              const min = parseInt(split[1])
              // @ts-ignore
              if (form.properties[key].value.length < min) {
                someErrors++
                create.errors(form.properties, key)
                create.messages(form.properties, key, rule)
              } else {
                create.errors(form.properties, key)
                create.messages(form.properties, key, rule)
              }
              // @ts-ignore
            } else if (
              ['popupfield', 'selectfield'].includes(
                form.properties[key].type,
              ) &&
              form.properties[key].options.multiple &&
              rule.includes('max:')
            ) {
              const split = rule.split(':')
              const max = parseInt(split[1])
              // @ts-ignore
              if (form.properties[key].value.length > max) {
                someErrors++
                create.errors(form.properties, key)
                create.messages(form.properties, key, rule)
              } else {
                create.errors(form.properties, key)
                create.messages(form.properties, key, rule)
              }
            } else if (
              form.properties[key].type === 'passwordfield' &&
              rule.includes('min_number:')
            ) {
              const str = form.properties[key].value as string
              const digitCount = CheckString(str, 'number')
              const split = rule.split(':')
              const min = parseInt(split[1])
              if (digitCount < min) {
                someErrors++
                create.errors(form.properties, key)
                create.messages(form.properties, key, rule)
              } else {
                create.errors(form.properties, key)
                create.messages(form.properties, key, rule)
              }
            } else if (
              form.properties[key].type === 'passwordfield' &&
              rule.includes('max_number:')
            ) {
              const str = form.properties[key].value as string
              const digitCount = CheckString(str, 'number')
              const split = rule.split(':')
              const max = parseInt(split[1])
              if (digitCount > max) {
                someErrors++
                create.errors(form.properties, key)
                create.messages(form.properties, key, rule)
              } else {
                create.errors(form.properties, key)
                create.messages(form.properties, key, rule)
              }
            } else if (
              form.properties[key].type === 'passwordfield' &&
              rule.includes('min_special:')
            ) {
              const str = form.properties[key].value as string
              const digitCount = CheckString(str, 'special')
              const split = rule.split(':')
              const min = parseInt(split[1])
              if (digitCount < min) {
                someErrors++
                create.errors(form.properties, key)
                create.messages(form.properties, key, rule)
              } else {
                create.errors(form.properties, key)
                create.messages(form.properties, key, rule)
              }
            } else if (
              form.properties[key].type === 'passwordfield' &&
              rule.includes('max_special:')
            ) {
              const str = form.properties[key].value as string
              const digitCount = CheckString(str, 'special')
              const split = rule.split(':')
              const max = parseInt(split[1])
              if (digitCount > max) {
                someErrors++
                create.errors(form.properties, key)
                create.messages(form.properties, key, rule)
              } else {
                create.errors(form.properties, key)
                create.messages(form.properties, key, rule)
              }
            } else if (
              form.properties[key].type === 'passwordfield' &&
              rule.includes('min_lowercase:')
            ) {
              const str = form.properties[key].value as string
              const digitCount = CheckString(str, 'lowercase')
              const split = rule.split(':')
              const min = parseInt(split[1])
              if (digitCount < min) {
                someErrors++
                create.errors(form.properties, key)
                create.messages(form.properties, key, rule)
              } else {
                create.errors(form.properties, key)
                create.messages(form.properties, key, rule)
              }
            } else if (
              form.properties[key].type === 'passwordfield' &&
              rule.includes('max_lowercase:')
            ) {
              const str = form.properties[key].value as string
              const digitCount = CheckString(str, 'lowercase')
              const split = rule.split(':')
              const max = parseInt(split[1])
              if (digitCount > max) {
                someErrors++
                create.errors(form.properties, key)
                create.messages(form.properties, key, rule)
              } else {
                create.errors(form.properties, key)
                create.messages(form.properties, key, rule)
              }
            } else if (
              form.properties[key].type === 'passwordfield' &&
              rule.includes('min_uppercase:')
            ) {
              const str = form.properties[key].value as string
              const digitCount = CheckString(str, 'uppercase')
              const split = rule.split(':')
              const min = parseInt(split[1])
              if (digitCount < min) {
                someErrors++
                create.errors(form.properties, key)
                create.messages(form.properties, key, rule)
              } else {
                create.errors(form.properties, key)
                create.messages(form.properties, key, rule)
              }
            } else if (
              form.properties[key].type === 'passwordfield' &&
              rule.includes('max_uppercase:')
            ) {
              const str = form.properties[key].value as string
              const digitCount = CheckString(str, 'uppercase')
              const split = rule.split(':')
              const max = parseInt(split[1])
              if (digitCount > max) {
                someErrors++
                create.errors(form.properties, key)
                create.messages(form.properties, key, rule)
              } else {
                create.errors(form.properties, key)
                create.messages(form.properties, key, rule)
              }
            } else if (
              form.properties[key].type === 'passwordfield' &&
              rule.includes('match:')
            ) {
              const split = rule.split(':')
              const field = split[1]
              const str = form.properties[field].value as string
              if (form.properties[key].value !== str) {
                someErrors++
                create.errors(form.properties, key)
                create.messages(form.properties, key, rule)
              } else {
                create.errors(form.properties, key)
                create.messages(form.properties, key, rule)
              }
            } else if (
              form.properties[key].type === 'textfield' &&
              rule.includes('no_space')
            ) {
              const str = form.properties[key].value as string
              const digitCount = CheckString(str, 'space')
              if (digitCount > 0) {
                someErrors++
                create.errors(form.properties, key)
                create.messages(form.properties, key, rule)
              } else {
                create.errors(form.properties, key)
                create.messages(form.properties, key, rule)
              }
            } else if (
              form.properties[key].type === 'textfield' &&
              rule.includes('no_special')
            ) {
              const str = form.properties[key].value as string
              const digitCount = CheckString(str, 'special')
              if (digitCount > 0) {
                someErrors++
                create.errors(form.properties, key)
                create.messages(form.properties, key, rule)
              } else {
                create.errors(form.properties, key)
                create.messages(form.properties, key, rule)
              }
            } else if (
              form.properties[key].type === 'numberfield' &&
              rule.includes('max:')
            ) {
              const split = rule.split(':')
              const max = parseInt(split[1])
              if (form.properties[key].value > max) {
                someErrors++
                create.errors(form.properties, key)
                create.messages(form.properties, key, rule)
              } else {
                create.errors(form.properties, key)
                create.messages(form.properties, key, rule)
              }
              // @ts-ignore
            } else if (
              form.properties[key].type === 'numberfield' &&
              rule.includes('min:')
            ) {
              const split = rule.split(':')
              const min = parseInt(split[1])
              if (form.properties[key].value < min) {
                someErrors++
                create.errors(form.properties, key)
                create.messages(form.properties, key, rule)
              } else {
                create.errors(form.properties, key)
                create.messages(form.properties, key, rule)
              }
            } else if (
              form.properties[key].type === 'numberfield' &&
              rule.includes('decimal')
            ) {
              const split = rule.split(':')
              const digits = parseInt(split[1])
              if (form.properties[key].value) {
                const val = form.properties[key].value.toString()
                const split2 = val.split('.')
                if (split2.length === 2) {
                  const currDigits = split2[1].length
                  if (digits < currDigits) {
                    someErrors++
                    create.errors(form.properties, key)
                    create.messages(form.properties, key, rule)
                  } else {
                    create.errors(form.properties, key)
                    create.messages(form.properties, key, rule)
                  }
                }
              }
            } else if (
              rule.indexOf('mimes:') > -1 &&
              form.properties[key].value
            ) {
              if (form.properties[key].value) {
                let types: any = rule.replace('mimes:', '')
                types = types.split(',')
                // @ts-ignore
                const cType = form.properties[key].value.type
                  .replace(
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'xlsx',
                  )
                  .replace('application/vnd.ms-excel', 'xls')
                  .replace(
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'docx',
                  )
                  .replace('application/msword', 'doc')
                let match = 0
                types.forEach((type) => {
                  if (cType.indexOf(type) > -1) {
                    match++
                  }
                })

                if (!match) {
                  someErrors++
                  create.errors(form.properties, key)
                  create.messages(form.properties, key, 'mimes')
                } else {
                  remove.messages(form.properties, key, 'mimes')
                }
              } else {
                create.errors(form.properties, key)
                create.messages(form.properties, key, 'mimes')
              }
            }
          })
        }
      }

      check(mode)
    }
  })

  if (!form.properties.errors) {
    form.properties.errors = function (formName) {
      if (formName) {
        const check = (mode: 'create' | 'edit' | 'preview' | '') => {
          if (this[formName].rules && this[formName].rules.length) {
            let is = 0 // i stop
            this[formName].rules.forEach((e, ei) => {
              if (!is) {
                if (e.includes('required')) {
                  if (
                    (this[formName].type === 'selectfield' &&
                      this[formName].options.multiple) ||
                    this[formName].type === 'tagsfield'
                  ) {
                    if (!this[formName].value.length) {
                      this[formName].currentError = ei
                      is++
                    }
                  } else if (this[formName].type === 'numberfield') {
                    if (
                      this[formName].value === null ||
                      this[formName].value === undefined ||
                      this[formName].value === ''
                    ) {
                      this[formName].currentError = ei
                      is++
                    }
                  } else if (this[formName].type === 'filefield') {
                    if (mode === 'edit') {
                      if (!this[formName].value && this[formName].hasChanged) {
                        this[formName].currentError = ei
                        is++
                      }
                    } else if (mode === 'create') {
                      if (!this[formName].value) {
                        this[formName].currentError = ei
                        is++
                      }
                    }
                  } else {
                    if (!this[formName].value) {
                      this[formName].currentError = ei
                      is++
                    }
                  }
                } else if (
                  ['textfield', 'passwordfield', 'textareafield'].includes(
                    this[formName].type,
                  ) &&
                  e.includes('max:')
                ) {
                  const split = e.split(':')
                  const max = parseInt(split[1])
                  if (this[formName].value.length > max) {
                    this[formName].currentError = ei
                    is++
                  }
                } else if (
                  ['textfield', 'passwordfield', 'textareafield'].includes(
                    this[formName].type,
                  ) &&
                  e.includes('min:')
                ) {
                  const split = e.split(':')
                  const min = parseInt(split[1])
                  if (this[formName].value.length < min) {
                    this[formName].currentError = ei
                    is++
                  }
                } else if (
                  ['popupfield', 'selectfield'].includes(this[formName].type) &&
                  this[formName].options.multiple &&
                  e.includes('max:')
                ) {
                  const split = e.split(':')
                  const max = parseInt(split[1])
                  if (this[formName].value.length > max) {
                    this[formName].currentError = ei
                    is++
                  }
                } else if (
                  ['popupfield', 'selectfield'].includes(this[formName].type) &&
                  this[formName].options.multiple &&
                  e.includes('min:')
                ) {
                  const split = e.split(':')
                  const min = parseInt(split[1])
                  if (this[formName].value.length < min) {
                    this[formName].currentError = ei
                    is++
                  }
                } else if (
                  this[formName].type === 'numberfield' &&
                  e.includes('max:')
                ) {
                  const split = e.split(':')
                  const max = parseInt(split[1])
                  if (this[formName].value > max) {
                    this[formName].currentError = ei
                    is++
                  }
                } else if (
                  this[formName].type === 'numberfield' &&
                  e.includes('min:')
                ) {
                  const split = e.split(':')
                  const min = parseInt(split[1])
                  if (this[formName].value < min) {
                    this[formName].currentError = ei
                    is++
                  }
                } else if (
                  this[formName].type === 'passwordfield' &&
                  e.includes('max_number:')
                ) {
                  const str = this[formName].value as string
                  const digitCount = CheckString(str, 'number')
                  const split = e.split(':')
                  const max = parseInt(split[1])
                  if (digitCount > max) {
                    this[formName].currentError = ei
                    is++
                  }
                } else if (
                  this[formName].type === 'passwordfield' &&
                  e.includes('min_number:')
                ) {
                  const str = this[formName].value as string
                  const digitCount = CheckString(str, 'number')
                  const split = e.split(':')
                  const min = parseInt(split[1])
                  if (digitCount < min) {
                    this[formName].currentError = ei
                    is++
                  }
                } else if (
                  this[formName].type === 'passwordfield' &&
                  e.includes('max_special:')
                ) {
                  const str = this[formName].value as string
                  const digitCount = CheckString(str, 'special')
                  const split = e.split(':')
                  const max = parseInt(split[1])
                  if (digitCount > max) {
                    this[formName].currentError = ei
                    is++
                  }
                } else if (
                  this[formName].type === 'passwordfield' &&
                  e.includes('min_special:')
                ) {
                  const str = this[formName].value as string
                  const digitCount = CheckString(str, 'special')
                  const split = e.split(':')
                  const min = parseInt(split[1])
                  if (digitCount < min) {
                    this[formName].currentError = ei
                    is++
                  }
                } else if (
                  this[formName].type === 'passwordfield' &&
                  e.includes('max_lowercase:')
                ) {
                  const str = this[formName].value as string
                  const digitCount = CheckString(str, 'lowercase')
                  const split = e.split(':')
                  const max = parseInt(split[1])
                  if (digitCount > max) {
                    this[formName].currentError = ei
                    is++
                  }
                } else if (
                  this[formName].type === 'passwordfield' &&
                  e.includes('min_lowercase:')
                ) {
                  const str = this[formName].value as string
                  const digitCount = CheckString(str, 'lowercase')
                  const split = e.split(':')
                  const min = parseInt(split[1])
                  if (digitCount < min) {
                    this[formName].currentError = ei
                    is++
                  }
                } else if (
                  this[formName].type === 'passwordfield' &&
                  e.includes('max_uppercase:')
                ) {
                  const str = this[formName].value as string
                  const digitCount = CheckString(str, 'uppercase')
                  const split = e.split(':')
                  const max = parseInt(split[1])
                  if (digitCount > max) {
                    this[formName].currentError = ei
                    is++
                  }
                } else if (
                  this[formName].type === 'passwordfield' &&
                  e.includes('min_uppercase:')
                ) {
                  const str = this[formName].value as string
                  const digitCount = CheckString(str, 'uppercase')
                  const split = e.split(':')
                  const min = parseInt(split[1])
                  if (digitCount < min) {
                    this[formName].currentError = ei
                    is++
                  }
                } else if (
                  this[formName].type === 'passwordfield' &&
                  e.includes('match:')
                ) {
                  const split = e.split(':')
                  const field = split[1]
                  const str = this[field].value as string
                  if (this[formName].value !== str) {
                    this[formName].currentError = ei
                    is++
                  }
                } else if (
                  this[formName].type === 'textfield' &&
                  e.includes('no_space')
                ) {
                  const str = this[formName].value as string
                  const digitCount = CheckString(str, 'space')
                  if (digitCount > 0) {
                    this[formName].currentError = ei
                    is++
                  }
                } else if (
                  this[formName].type === 'textfield' &&
                  e.includes('no_special')
                ) {
                  const str = this[formName].value as string
                  const digitCount = CheckString(str, 'special')
                  if (digitCount > 0) {
                    this[formName].currentError = ei
                    is++
                  }
                } else if (
                  this[formName].type === 'numberfield' &&
                  e.includes('decimal')
                ) {
                  const split = e.split(':')
                  const digits = parseInt(split[1])
                  if (this[formName].value) {
                    const val = this[formName].value.toString()
                    const split2 = val.split('.')
                    if (split2.length === 2) {
                      const currDigits = split2[1].length
                      if (digits < currDigits) {
                        this[formName].currentError = ei
                        is++
                      }
                    }
                  }
                } else if (e.includes('mimes')) {
                  if (this[formName].value) {
                    let types = e.replace('mimes:', '')
                    types = types.split(',')
                    const cType = this[formName].value.type
                    let match = 0
                    types.forEach((type) => {
                      if (cType.indexOf(type) > -1) {
                        match++
                      }
                    })

                    if (!match) {
                      this[formName].currentError = ei
                      is++
                    }
                  }
                }
              }
            })

            if (is) {
              if (this[formName].errors) {
                return this[formName].errors[this[formName].currentError]
              }
            }
          }
        }

        return check(mode)
      }
    }
  }

  return someErrors
}
