import ObjectUpdater from '~/services/ObjectUpdater'
import ObjectReader from '~/services/ObjectReader'

export default {
  get: (rows, rowIndex, rowId, field) => {
    if (!rowId) {
      return ObjectReader(rows[rowIndex], field)
    } else {
      const row = rows.filter((row) => row.id === rowId)[0]
      return ObjectReader(row, field)
    }
  },
  update: (rows, rowIndex, rowId, field, value) => {
    if (!rowId) {
      ObjectUpdater(rows[rowIndex], field, value)
    } else {
      const row = rows.filter((row) => row.id === rowId)[0]
      ObjectUpdater(row, field, value)
    }
  },
}
