import { FormDetail } from '~/types/form/detail'
import ObjectReader from '~/services/ObjectReader'
import { messagesDtl } from '../helper'
import { FormDetailPropertiesFixer } from '~/helpers'

const getColumnName = (properties: Array<any>, field: string) => {
  let label = 'Uknown'
  properties.forEach((pro) => {
    if (pro.field === field) {
      label = pro.label
    }
  })
  return label
}

export default (inform: FormDetail, rowIndex, formName) => {
  if (formName) {
    const check = () => {
      const errors = []
      const value = ObjectReader(inform.rows[rowIndex], formName)
      inform.includes
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
                if (
                  ['selectfield', 'popupfield', 'pinpointfield'].includes(
                    pro.editor.type,
                  )
                ) {
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
                } else if (pro.editor.type === 'subdetailfield') {
                  if (!value.length) {
                    errors.push(messagesDtl[rule])
                  }
                } else if (value === undefined || value === null) {
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
              } else if (
                rule.includes('max') &&
                ['selectfield', 'popupfield', 'pinpointfield'].includes(
                  pro.editor.type,
                ) &&
                pro.editor.options.multiple
              ) {
                const split = rule.split(':')
                const max: number = parseInt(split[1])
                if (value.length > max) {
                  let message
                  if (pro.editor.type === 'pinpointfield') {
                    message = messagesDtl.maxLoc.replace(':max', max.toString())
                  } else {
                    message = messagesDtl.maxItem.replace(
                      ':max',
                      max.toString(),
                    )
                  }
                  errors.push(message)
                }
              } else if (
                rule.includes('min') &&
                ['selectfield', 'popupfield', 'pinpointfield'].includes(
                  pro.editor.type,
                ) &&
                pro.editor.options.multiple
              ) {
                const split = rule.split(':')
                const min: number = parseInt(split[1])
                if (value.length < min) {
                  let message
                  if (pro.editor.type === 'pinpointfield') {
                    message = messagesDtl.minLoc.replace(':min', min.toString())
                  } else {
                    message = messagesDtl.minItem.replace(
                      ':min',
                      min.toString(),
                    )
                  }
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
                if (cType) {
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
              } else if (
                ['numberfield', 'rupiahfield'].includes(pro.editor.type)
              ) {
                if (rule.includes('lt:')) {
                  const split = rule.split(':')
                  const field: string = split[1]
                  const column = getColumnName(
                    fixProperties.filterProperties,
                    field,
                  )
                  if (value < inform.rows[rowIndex][field]) {
                    //
                  } else if (value) {
                    const message = messagesDtl.ltColumn.replace(
                      ':column',
                      column.toLowerCase(),
                    )
                    errors.push(message)
                  }
                } else if (rule.includes('lte:')) {
                  const split = rule.split(':')
                  const field: string = split[1]
                  const column = getColumnName(
                    fixProperties.filterProperties,
                    field,
                  )
                  if (value <= inform.rows[rowIndex][field]) {
                    //
                  } else if (value) {
                    const message = messagesDtl.lteColumn.replace(
                      ':column',
                      column.toLowerCase(),
                    )
                    errors.push(message)
                  }
                } else if (rule.includes('gt:')) {
                  const split = rule.split(':')
                  const field: string = split[1]
                  const column = getColumnName(
                    fixProperties.filterProperties,
                    field,
                  )
                  if (value > inform.rows[rowIndex][field]) {
                    //
                  } else if (value) {
                    const message = messagesDtl.gtColumn.replace(
                      ':column',
                      column.toLowerCase(),
                    )
                    errors.push(message)
                  }
                } else if (rule.includes('gte:')) {
                  const split = rule.split(':')
                  const field: string = split[1]
                  const column = getColumnName(
                    fixProperties.filterProperties,
                    field,
                  )
                  if (value >= inform.rows[rowIndex][field]) {
                    //
                  } else if (value) {
                    const message = messagesDtl.gteColumn.replace(
                      ':column',
                      column.toLowerCase(),
                    )
                    errors.push(message)
                  }
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
