import {FormSubDetail} from "~/types/form/subdetail";
import ObjectReader from "~/services/ObjectReader";
import Counter from "~/services/Validator/detail/counter";
import Errors from "~/services/Validator/subdetail/errors";
import { FormDetailPropertiesFixer } from '~/helpers'

export default (form: FormSubDetail, rows) => {
  let someErrors = 0
  rows.forEach((row) => {
    const fixProperties = FormDetailPropertiesFixer(form.properties)

    fixProperties.filterProperties.forEach((pro) => {
      const value = ObjectReader(row, pro.field)
      if (pro.type === 'editor' && pro.editor.type !== 'switchfield') {
        if (pro.editor.rules && pro.editor.rules.length) {
          pro.editor.rules.forEach((rule) => {
            rule = rule.toLowerCase()
            someErrors += Counter(pro, rule, value)
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