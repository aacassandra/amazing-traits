import { ObjectReader, ObjectUpdater } from '~/services/index'
import { FormDetail } from '~/types/form/detail'
import { Schema } from '~/types'

export default (schema: Schema, value: any, key?: string) => {
  schema.forms.forEach((Form) => {
    if (Form.type === 'header') {
      if (Form.properties[key]) {
        Form.properties[key].outVal = value
      }
    } else if (Form.type === 'detail' && Form.relation.column === key) {
      const item: FormDetail = Form
      const rows = value as Array<any>
      if (item.transform && item.transform.length) {
        rows.forEach((row) => {
          item.transform.forEach((tr) => {
            const trSplit = tr.split(':')
            if (trSplit.length === 2) {
              ObjectUpdater(row, trSplit[0], ObjectReader(row, trSplit[1]))
            }
          })
        })
      }

      if (Array.isArray(item.properties)) {
        item.properties.forEach((pro) => {
          if (pro.type === 'editor') {
            if (
              pro.editor.type === 'daterangefield' ||
              pro.editor.type === 'datetimerangefield'
            ) {
              const { start, end } = pro.editor.options.getFrom
              rows.forEach((row, rowI) => {
                if (row[start] && row[end]) {
                  ObjectUpdater(rows[rowI], pro.field, {
                    start: row[start],
                    end: row[end],
                  })
                }
              })
            } else if (pro.editor.type === 'subdetailfield') {
              rows.forEach((row, rowI) => {
                if (!ObjectReader(row, pro.field)) {
                  ObjectUpdater(rows[rowI], pro.field, [])
                }
              })
            }
          }
        })
      }

      Form.rows = rows
    }
  })
}
