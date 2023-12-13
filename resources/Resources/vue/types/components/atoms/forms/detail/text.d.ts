import { Element } from '~/types/components/atoms/forms/detail/index'
import { Text } from '~/types/form/form-v1'

export type ListenerType = 'init' | 'change' | 'blur' | 'focus' | 'keydown' | 'keyup'

export interface TextListenerConfig {
  exclude: Array<ListenerType>
}

export interface TextfieldDtlOptions {
  information?: Text
  placeholder?: Text
  readonly?: boolean
  disabled?: boolean
  listener?: TextListenerConfig
}

export interface TextfieldDtl {
  type: 'textfield'
  rules?: Array<string>
  options?: TextfieldDtlOptions
  listener?: (element: Element, type: ListenerType, value: any) => void
  default?: 'inherit' | string
}
