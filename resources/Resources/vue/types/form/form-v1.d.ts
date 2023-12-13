import { FormHeader } from '~/types/form/header'
import { FormDetail } from '~/types/form/detail'

export type Forms = Array<FormHeader | FormDetail>

export interface SchemaV1 {
  version: 1
  isReady?: boolean
  mode: 'create' | 'edit' | 'preview' | ''
  forms: Array<FormHeader | FormDetail>
}

// image and div has implemented in popupfield of header
export type TableColumns = Array<
  TableColumnDefault | TableColumnImage | TableColumnHTML
>

export interface TableColumnDefault {
  // when use boolean type: justify-content-right
  // when use bool type: justify-content-left
  type:
    | 'string'
    | 'number'
    | 'decimal'
    | 'date'
    | 'percentage'
    | 'boolean'
    | 'rupiah'
  width?: string
  model?: string
  show?: boolean
  field?: string
  style?: any
  conditional?(
    row: { [key: string]: any },
    rows: Array<{ [key: string]: any }>,
    index: number,
  ): any
  label?: Text
  thClass?: string
  tdClass?: string
  dateInputFormat?: string // expects 2018-03-16
  dateOutputFormat?: string
  sortable?: boolean
  filter?: boolean
  templates?: string | Array<string>
  filterOptions?: {
    enabled: boolean
    placeholder?: string
    trigger?: 'enter'
    filterDropdownItems?: Array<{ value: any; text: string }>
  }
}

export interface TableColumnImage {
  type: 'image'
  style?: any
  show?: boolean
  viewer?: boolean
  width?: string
  field?: string
  label?: Text
}

export interface TableColumnHTML {
  type: 'div'
  style?: any
  show?: boolean
  html?: string
  width?: string
  sortable?: boolean
  field?: string
  label?: Text
}

export interface Lang {
  value: string
  options?: {
    [key: string]: string
  }
}
export type Text = string | Lang
