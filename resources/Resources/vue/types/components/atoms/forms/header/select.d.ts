import {
  ApiInterface,
  Element,
} from '~/types/components/atoms/forms/header/index'
import { Text } from '~/types/form/form-v1'

export interface Field {
  value: string
  display: string | Array<any>
  type?: {
    // default string
    value: 'encrypted' | 'string' | 'number' | 'boolean'
    display: 'encrypted' | 'string' | 'number' | 'boolean'
  }
}
export interface SelectfieldOptionsV1 {
  label: Text
  information?: Text
  inline: boolean
  placeholder?: Text
  readonly?: boolean
  disabled?: boolean
  hidden?: boolean
  multiple?: boolean
  reduce?(row: any): void
  field?: Field
  api?: ApiInterface
}

export interface SelectfieldOptionsV2 {
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
  field?: Field
  api?: ApiInterface
}

export interface SelectfieldV1 {
  type: 'selectfield'
  version: number
  outVal?: string | number | boolean | Array<any>
  value: string | number | boolean | Array<any>
  items?: Array<
    | {
        [key: string]: any
      }
    | string
  >
  col: string
  options: SelectfieldOptionsV1
  listener?: (element: Element, type: ListenerTypeV2, value: any) => void
  rules?: Array<string>
  hasChanged?: boolean
  default?: 'inherit' | string | number | boolean | Array<any>
  setValue?(value: any): void
  getValueFull?(): void
}

type ListenerTypeV2 =
  | 'bluring'
  | 'blur'
  | 'focusing'
  | 'focus'
  | 'selecting'
  | 'select'
  | 'unselecting'
  | 'unselect'
  | 'clearing'
  | 'clear'
  | 'change'

export interface SelectfieldV2 {
  type: 'selectfield'
  version: number
  outVal?: string | number | boolean | Array<any>
  value: string | number | boolean | Array<any>
  items?: Array<
    | {
        [key: string]: any
      }
    | string
  >
  col: string
  options: SelectfieldOptionsV2
  listener?: (element: Element, type: ListenerTypeV2, value: any) => void
  rules?: Array<string>
  hasChanged?: boolean
  default?: 'inherit' | string | number | boolean | Array<any>
  setValue?(value: any): void
  getValueFull?(): void
}

export interface SelectfieldV3 {
  type: 'selectfield'
  version: number
  outVal?: string | number | boolean | Array<any>
  value: string | number | boolean | Array<any>
  items?: Array<
    | {
        [key: string]: any
      }
    | string
  >
  col: string
  options: SelectfieldOptionsV2
  listener?: (element: Element, type: ListenerTypeV2, value: any) => void
  rules?: Array<string>
  hasChanged?: boolean
  default?: 'inherit' | string | number | boolean | Array<any>
  setValue?(value: any): void
  getValueFull?(): void
}

export type Selectfield = SelectfieldV1 | SelectfieldV2 | SelectfieldV3
