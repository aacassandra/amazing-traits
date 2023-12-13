import { Element } from '~/types/components/atoms/forms/detail/index'
import { Text } from '~/types/form/form-v1'

type ListenerType =
  | 'change'
  | 'blur'
  | 'focus'
  | 'keydown'
  | 'keyup'
  | 'mounted'
  | 'created'

export interface TextAreaListenerConfig {
  exclude: Array<ListenerType>
}

export interface TextAreafieldDtlOptions {
  information?: Text
  placeholder?: Text
  readonly?: boolean
  disabled?: boolean
  listener?: TextAreaListenerConfig
}

export interface TextAreafieldDtl {
  type: 'textareafield'
  rules?: Array<string>
  options?: TextAreafieldDtlOptions
  listener?: (element: Element, type: ListenerType, value: any) => void
  default?: 'inherit' | string
}
