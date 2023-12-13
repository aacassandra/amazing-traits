import { Element } from '~/types/components/atoms/forms/detail/index'
import { Text } from '~/types/form/form-v1'

export interface PasswordfieldDtlOptions {
  information?: Text
  placeholder?: Text
  readonly?: boolean
  disabled?: boolean
}

export type ListenerType = string

export interface PasswordfieldDtl {
  type: 'passwordfield'
  rules?: Array<string>
  options?: PasswordfieldDtlOptions
  listener?: (element: Element, type: ListenerType, value: any) => void
  default?: 'inherit' | string
}
