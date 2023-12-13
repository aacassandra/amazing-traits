import { Element } from '~/types/components/atoms/forms/detail/index'
import { Text } from '~/types/form/form-v1'
import { ApiInterface } from '~/types/components/atoms/forms/header'
import { TableColumns } from '~/types/form/form-v1'
import { TableConfigs } from '~/types/form/detail'
import { Field } from '~/types/components/atoms/forms/header/popup'

export interface PopupfieldDtlOptions {
  information?: Text
  placeholder?: Text
  readonly?: boolean
  disabled?: boolean
  multiple?: boolean
  clearable?: boolean
  reduce?(row: any): void
  tableConfigs?: TableConfigs
  field: Field
  api?: ApiInterface
}

export type ListenerType = 'change' | 'focus' | 'blur' | 'clear'

export interface PopupfieldDtl {
  type: 'popupfield'
  options?: PopupfieldDtlOptions
  listener?: (element: Element, type: ListenerType, value: any) => void
  rules?: Array<string>
  default?: 'inherit' | string | number | boolean | Array<any>
}
