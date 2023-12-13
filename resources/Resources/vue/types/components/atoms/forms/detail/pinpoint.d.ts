import { Element } from '~/types/components/atoms/forms/detail/index'
import { Text } from '~/types/form/form-v1'

export interface PinpointfieldDtlOptions {
  information?: Text
  placeholder?: Text
  readonly?: boolean
  disabled?: boolean
  multiple?: boolean
  clearable?: boolean
}

export type ListenerType = 'change' | 'focus' | 'blur' | 'clear'

export interface PinpointfieldDtl {
  type: 'pinpointfield'
  options?: PinpointfieldDtlOptions
  listener?: (element: Element, type: ListenerType, value: any) => void
  rules?: Array<string>
  default?: 'inherit' | string | number | boolean | Array<any>
}
