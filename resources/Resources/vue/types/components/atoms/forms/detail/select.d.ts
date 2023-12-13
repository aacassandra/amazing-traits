import { Element } from '~/types/components/atoms/forms/detail/index'
import { Text } from '~/types/form/form-v1'
import { ApiInterface } from '~/types/components/atoms/forms/header'
import { ListenerType } from '~/types/components/atoms/forms/detail/daterange'
import { Field } from '~/types/components/atoms/forms/header/select'

export interface SelectfiedDtlOptionsV1 {
  information?: Text
  placeholder?: Text
  readonly?: boolean
  disabled?: boolean
  multiple?: boolean
  reduce?(row: any): void
  field?: Field
  api?: ApiInterface
  items?: Array<
    | {
        [key: string]: any
      }
    | string
    | number
  >
}

export interface SelectfiedDtlOptionsV2 {
  information?: Text
  placeholder?: Text
  readonly?: boolean
  disabled?: boolean
  multiple?: boolean
  clearable?: boolean
  reduce?(row: any): void
  field?: Field
  api?: ApiInterface
  items?: Array<
    | {
        [key: string]: any
      }
    | string
    | number
  >
  disabledItems?: Array<any> // sementara ini hanya support untuk items hardcode
}

export interface SelectfiedDtlOptionsV3 {
  information?: Text
  placeholder?: Text
  readonly?: boolean
  disabled?: boolean
  multiple?: boolean
  clearable?: boolean
  reduce?(row: any): void
  field?: {
    value: string
    display: string | Array<any>
  }
  api?: ApiInterface
  items?: Array<
    | {
        [key: string]: any
      }
    | string
    | number
  >
}

// version 1
export interface SelectfiedDtlV1 {
  type: 'selectfield'
  version: number
  options?: SelectfiedDtlOptionsV1
  listener?: (element: Element, type: ListenerTypeV2, value: any) => void
  rules?: Array<string>
  default?: 'inherit' | string | number | boolean | Array<any>
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

// version 2
export interface SelectfiedDtlV2 {
  type: 'selectfield'
  version: number
  options?: SelectfiedDtlOptionsV2
  listener?: (element: Element, type: ListenerTypeV2, value: any) => void
  rules?: Array<string>
  default?: 'inherit' | string | number | boolean | Array<any>
}

// version 2
export interface SelectfiedDtlV3 {
  type: 'selectfield'
  version: number
  options?: SelectfiedDtlOptionsV3
  listener?: (element: Element, type: ListenerTypeV2, value: any) => void
  rules?: Array<string>
  default?: 'inherit' | string | number | boolean | Array<any>
}

export type SelectfieldDtl = SelectfiedDtlV1 | SelectfiedDtlV2 | SelectfiedDtlV3
