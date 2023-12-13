import { FormDetail } from '~/types/form/detail'
import ObjectReader from '~/services/ObjectReader'
import Counter from './counter'
import Errors from './errors'
import { FormDetailPropertiesFixer } from '~/helpers'

export default (form: FormDetail) => {
  let someErrors = 0
  form.rows.forEach((row) => {
    const fixProperties = FormDetailPropertiesFixer(form.properties)

    fixProperties.filterProperties.forEach((pro) => {
      const value = ObjectReader(row, pro.field)
      if (pro.type === 'editor' && pro.editor.type !== 'switchfield') {
        if (pro.editor.rules && pro.editor.rules.length) {
          pro.editor.rules.forEach((rule) => {
            rule = rule.toLowerCase()
            someErrors += Counter(pro, rule, value, row)
          })
        }
      }
    })

    if (!row.errors) {
      row.errors = Errors
    }
  })

  return someErrors
}
