// import {
//   PeriodicStyle1,
//   PeriodicStyle2,
//   PeriodicStyle3,
// } from '~/types/components/atoms/buttons/dropdown'
// import { RequestLandingConfigs } from '~/types/plugins/request'
import { TableColumns } from '~/types/form/form-v1'
import { TableConfigs } from '~/types/form/detail'
import { OldParams } from '~/types/components/atoms/forms/detail'

// export type DtPgIndex = {
//   periodics: Array<PeriodicStyle1 | PeriodicStyle2 | PeriodicStyle3>
// }

export interface LandingColumn {
  style?: any
  field?: string
  model?: string // only support for postgre
  show?: boolean
  width?: string
  type?:
    | 'image'
    | 'div'
    | 'rupiah'
    | 'number'
    | 'decimal'
    | 'percentage'
    | 'boolean'
    | 'date'
  conditional?(
    row: { [key: string]: any },
    rows: Array<{ [key: string]: any }>,
    index: number,
  ): any
  label?: string
  dateInputFormat?: string
  dateOutputFormat?: string
  // string example use: <div class="p-1"><span class="bg-blue-500 p-1 rounded-md text-white">:value</span></div> | value will replaced with current value of row
  template?: string | Array<any>
  sortable?: boolean
  filter?: boolean
  filterOptions?: {
    enabled: boolean
    placeholder?: string
    trigger?: 'enter'
    filterDropdownItems?: Array<{ value: any; text: string }>
  }
}

export interface DataLanding {
  isRefresh: boolean
  isLoading: boolean
  disabledAutoLoad?: boolean
  api: {
    url: string
    root?: string
    parameters?: {
      [key: string]: any
    }
    overrideParams?(oldParams: OldParams): OldParams
  }
  columns: TableColumns
  fixColumns?: TableColumns
  tableConfigs?: TableConfigs
  rows: Array<any>
  groups?: {
    [key: string]: any
  }
  totalRecords: number
  serverParams: {
    columnFilters: { [key: string]: any }
    sort: Array<any>
    page: number
    perPage: number
  }
  selectedRow?: {
    pageIndex: number
    row: any
  }
  actions: any
}
