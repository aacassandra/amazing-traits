import { TableColumns } from '~/types/form/form-v1'

const checkFixValue = (col, val) => {
  let fixValue = null
  if (col.type === 'boolean') {
    if (col.templates && Array.isArray(col.templates)) {
      col.templates.forEach((temp) => {
        const spt = temp.split('|')
        if (spt.length === 3) {
          if (val == spt[0]) {
            fixValue = JSON.parse(spt[0])
          }
        }
      })
    } else {
      fixValue = val === 'true'
    }
  } else if (col.type === 'number') {
    fixValue = parseInt(val)
  } else {
    fixValue = val
  }

  return fixValue
}

export const FixColumnFilter = (finalProps, columns: TableColumns) => {
  finalProps = JSON.parse(JSON.stringify(finalProps))
  if (finalProps.columnFilters) {
    Object.entries(finalProps.columnFilters).forEach(([key, val]) => {
      if (!val) {
        delete finalProps.columnFilters[key]
      } else {
        columns.forEach((col) => {
          const type = col.type || undefined
          if (key === col.field) {
            const fixValue = checkFixValue(col, val)

            if (val) {
              if (col.type !== 'image' && col.type !== 'div' && col.model) {
                // only support for postgre
                finalProps.columnFilters[`${col.model}.${key}`] = fixValue
                delete finalProps.columnFilters[key]
              } else {
                finalProps.columnFilters[key] = fixValue
              }
            } else {
              delete finalProps.columnFilters[key]
            }
          }
        })
      }
    })
  }

  return finalProps
}
