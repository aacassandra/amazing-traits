import { TableColumnV1 } from '~/types/components/atoms/table/column'
import { DateRange } from '~/types/components/atoms/daterange'

export interface TableV1 {
  actions: {
    enabled: boolean
    buttons: {
      create: {
        enabled: boolean
        disabled: boolean
      }
      detail: {
        enabled: boolean
        disabled: boolean
      }
      edit: {
        enabled: boolean
        disabled: boolean
      }
      delete: {
        enabled: boolean
        disabled: boolean
      }
    }
    type: 'per-row' | 'global'
  }
  options: {
    select: {
      enabled: boolean
    }
    sort: {
      enabled: boolean
      initialSortBy: {
        field: string
        type: 'asc' | 'desc'
      }
    }
  }
  daterange: DateRange
  services: {
    enabled: boolean
    import: boolean | 'maintenance'
    export: boolean | 'maintenance'
    copy: boolean | 'maintenance'
  }
  columns: Array<TableColumnV1>
}
