/* eslint-disable @typescript-eslint/ban-ts-comment */
import ObjectReader from '~/services/ObjectReader'
import { messagesDtl } from '../helper'
import { FormSubDetail } from '~/types/form/subdetail'
import { FormDetailPropertiesFixer } from '~/helpers'

export default (
  rows: Array<any>,
  inform: FormSubDetail,
  rowIndex,
  formName,
) => {
  if (formName) {
    const check = () => {
      const errors = []
      const value = ObjectReader(rows[rowIndex], formName)
      const fixProperties = FormDetailPropertiesFixer(inform.properties)
      fixProperties.filterProperties.forEach((pro) => {
        if (pro.field === formName) {
          if (
            pro.type === 'editor' &&
            pro.editor.type !== 'switchfield' &&
            pro.editor.rules &&
            pro.editor.rules.length
          ) {
            pro.editor.rules.forEach((rule) => {
              rule = rule.toLowerCase()
              if (rule.includes('required')) {
                if (pro.editor.type === 'selectfield') {
                  if (pro.editor.options.multiple) {
                    if (!value.length) {
                      errors.push(messagesDtl[rule])
                    }
                  } else {
                    if (!value) {
                      errors.push(messagesDtl[rule])
                    }
                  }
                } else if (pro.editor.type === 'tagsfield') {
                  if (!value.length) {
                    errors.push(messagesDtl[rule])
                  }
                } else if (pro.editor.type === 'popupfield') {
                  if (pro.editor.options.multiple) {
                    if (!value.length) {
                      errors.push(messagesDtl[rule])
                    }
                  } else {
                    if (!value) {
                      errors.push(messagesDtl[rule])
                    }
                  }
                } else if (pro.editor.type === 'subdetailfield') {
                  if (!value.length) {
                    errors.push(messagesDtl[rule])
                  }
                } else if (!value) {
                  errors.push(messagesDtl[rule])
                }
              } else if (
                rule.includes('max') &&
                ['textfield', 'passwordfield', 'textareafield'].includes(
                  pro.editor.type,
                )
              ) {
                const split = rule.split(':')
                const max: number = parseInt(split[1])
                if (value.length > max) {
                  const message = messagesDtl.maxText.replace(
                    ':max',
                    max.toString(),
                  )
                  errors.push(message)
                }
              } else if (
                rule.includes('min') &&
                ['textfield', 'passwordfield', 'textareafield'].includes(
                  pro.editor.type,
                )
              ) {
                const split = rule.split(':')
                const min: number = parseInt(split[1])
                if (value.length < min) {
                  const message = messagesDtl.minText.replace(
                    ':min',
                    min.toString(),
                  )
                  errors.push(message)
                }
              } else if (
                rule.includes('max') &&
                pro.editor.type === 'numberfield'
              ) {
                const split = rule.split(':')
                const max: number = parseInt(split[1])
                if (value > max) {
                  const message = messagesDtl.maxNumber.replace(
                    ':max',
                    max.toString(),
                  )
                  errors.push(message)
                }
              } else if (
                rule.includes('min') &&
                pro.editor.type === 'numberfield'
              ) {
                const split = rule.split(':')
                const min: number = parseInt(split[1])
                if (value < min) {
                  const message = messagesDtl.minNumber.replace(
                    ':min',
                    min.toString(),
                  )
                  errors.push(message)
                }
                // @ts-ignore
              } else if (
                rule.includes('max') &&
                ['selectfield', 'popupfield'].includes(pro.editor.type) &&
                pro.editor.options.multiple
              ) {
                const split = rule.split(':')
                const max: number = parseInt(split[1])
                if (value.length > max) {
                  const message = messagesDtl.maxItem.replace(
                    ':max',
                    max.toString(),
                  )
                  errors.push(message)
                }
                // @ts-ignore
              } else if (
                rule.includes('min') &&
                ['selectfield', 'popupfield'].includes(pro.editor.type) &&
                pro.editor.options.multiple
              ) {
                const split = rule.split(':')
                const min: number = parseInt(split[1])
                if (value.length < min) {
                  const message = messagesDtl.minItem.replace(
                    ':min',
                    min.toString(),
                  )
                  errors.push(message)
                }
              } else if (
                rule.includes('max') &&
                pro.editor.type === 'subdetailfield'
              ) {
                const split = rule.split(':')
                const max: number = parseInt(split[1])
                if (value.length > max) {
                  const message = messagesDtl.maxItem.replace(
                    ':max',
                    max.toString(),
                  )
                  errors.push(message)
                }
              } else if (
                rule.includes('min') &&
                pro.editor.type === 'subdetailfield'
              ) {
                const split = rule.split(':')
                const min: number = parseInt(split[1])
                if (value.length < min) {
                  const message = messagesDtl.minItem.replace(
                    ':min',
                    min.toString(),
                  )
                  errors.push(message)
                }
              } else if (rule.indexOf('mimes:') > -1 && value) {
                let types: any = rule.replace('mimes:', '')
                types = types.split(',')
                const cType = value.type
                let match = 0
                types.forEach((type) => {
                  if (cType.indexOf(type) > -1) {
                    match++
                  }
                })

                if (!match) {
                  const message = messagesDtl.mimes
                  errors.push(message)
                }
              }
            })
          }
        }
      })

      if (errors.length) {
        return errors[0]
      } else {
        return
      }
    }

    return check()
  }
}
