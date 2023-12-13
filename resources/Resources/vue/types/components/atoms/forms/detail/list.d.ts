import { Element } from '~/types/components/atoms/forms/detail/index'
import { Text } from '~/types/form/form-v1'

export interface ListfieldDtlOptions {
  placeholder?: Text
  readonly?: boolean
  disabled?: boolean
}

type ListenerType =
  | 'add'
  | 'remove'
  | 'input'
  | 'edit'
  | 'invalid'
  | 'click'
  | 'focus'
  | 'blur'
  | 'change'

export interface ListfieldDtl {
  type: 'listfield'
  rules?: Array<string>
  options: ListfieldDtlOptions
  listener?: (element: Element, type: ListenerType, value: any) => void
  default?: Array<any>
}
