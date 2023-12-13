/* eslint-disable @typescript-eslint/ban-ts-comment */
import { FormDetailEditor } from '~/types/form/detail'

export default (pro: FormDetailEditor, rule: string, value: any, row = {}) => {
  let someErrors = 0
  if (rule.includes('required')) {
    if (
      pro.editor.type === 'selectfield' ||
      pro.editor.type === 'popupfield' ||
      pro.editor.type === 'pinpointfield'
    ) {
      if (pro.editor.options.multiple) {
        if (!value.length) {
          someErrors++
        }
      } else {
        if (!value) {
          someErrors++
        }
      }
    } else if (pro.editor.type === 'tagsfield') {
      if (!value.length) {
        someErrors++
      }
    } else if (pro.editor.type === 'subdetailfield') {
      if (!value.length) {
        someErrors++
      }
    } else if (value === undefined || value === null) {
      someErrors++
    }
  } else if (
    rule.includes('max') &&
    ['textfield', 'passwordfield', 'textareafield'].includes(pro.editor.type)
  ) {
    const split = rule.split(':')
    const max = parseInt(split[1])
    if (value.length > max) {
      someErrors++
    }
  } else if (
    rule.includes('min') &&
    ['textfield', 'passwordfield', 'textareafield'].includes(pro.editor.type)
  ) {
    const split = rule.split(':')
    const min = parseInt(split[1])
    if (value.length < min) {
      someErrors++
    }
  } else if (rule.includes('max') && pro.editor.type === 'numberfield') {
    const split = rule.split(':')
    const max = parseInt(split[1])
    if (value > max) {
      someErrors++
    }
  } else if (rule.includes('min') && pro.editor.type === 'numberfield') {
    const split = rule.split(':')
    const min = parseInt(split[1])
    if (value < min) {
      someErrors++
    }
  } else {
    if (
      rule.includes('max') &&
      ['selectfield', 'popupfield', 'pinpointfield'].includes(
        pro.editor.type,
      ) &&
      // @ts-ignore
      pro.editor.options.multiple
    ) {
      const split = rule.split(':')
      const max: number = parseInt(split[1])
      if (value.length > max) {
        someErrors++
      }
    } else {
      if (
        rule.includes('min') &&
        ['selectfield', 'popupfield', 'pinpointfield'].includes(
          pro.editor.type,
        ) &&
        // @ts-ignore
        pro.editor.options.multiple
      ) {
        const split = rule.split(':')
        const min: number = parseInt(split[1])
        if (value.length < min) {
          someErrors++
        }
      } else if (rule.includes('max') && pro.editor.type === 'subdetailfield') {
        const split = rule.split(':')
        const max: number = parseInt(split[1])
        if (value.length > max) {
          someErrors++
        }
      } else if (rule.includes('min') && pro.editor.type === 'subdetailfield') {
        const split = rule.split(':')
        const min: number = parseInt(split[1])
        if (value.length < min) {
          someErrors++
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
            someErrors++
          }
        }
      } else if (['numberfield', 'rupiahfield'].includes(pro.editor.type)) {
        if (rule.includes('lt:')) {
          const split = rule.split(':')
          const column: string = split[1]
          if (value < row[column]) {
            //
          } else if (value) {
            someErrors++
          }
        } else if (rule.includes('lte:')) {
          const split = rule.split(':')
          const column: string = split[1]
          if (value <= row[column]) {
            //
          } else if (value) {
            someErrors++
          }
        } else if (rule.includes('gt:')) {
          const split = rule.split(':')
          const column: string = split[1]
          if (value > row[column]) {
            //
          } else if (value) {
            someErrors++
          }
        } else if (rule.includes('gte:')) {
          const split = rule.split(':')
          const column: string = split[1]
          if (value >= row[column]) {
            //
          } else if (value) {
            someErrors++
          }
        }
      }
    }
  }

  return someErrors
}
