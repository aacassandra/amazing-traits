import {
  ApiInterface,
  Element,
} from '~/types/components/atoms/forms/header/index'
import { TableColumns } from '~/types/form/form-v1'
import { TableConfigs } from '~/types/form/detail'
import { Text } from '~/types/form/form-v1'

export interface Field {
  value: string
  display: string
  columns: TableColumns
  type?: {
    // default string
    value: 'encrypted' | 'string' | 'number' | 'boolean'
    display: 'encrypted' | 'string' | 'number' | 'boolean'
  }
}

export interface PopupfieldOptions {
  label: Text
  information?: Text
  inline: boolean
  placeholder?: Text
  readonly?: boolean
  disabled?: boolean
  hidden?: boolean
  multiple?: boolean
  clearable?: boolean
  reduce?(row: any): void
  tableConfigs?: TableConfigs
  field: Field
  api?: ApiInterface
}

export type ListenerType = 'change' | 'focus' | 'blur' | 'clear'

export interface Popupfield {
  type: 'popupfield'
  outVal?: string | number | boolean | Array<any>
  value: string | number | boolean | Array<any>
  items: Array<any>
  col: string
  options: PopupfieldOptions
  listener?: (element: Element, type: ListenerType, value: any) => void
  rules?: Array<string>
  hasChanged?: boolean
  default?: 'inherit' | string | number | boolean | Array<any>
  setValue?(value: any): void
}
