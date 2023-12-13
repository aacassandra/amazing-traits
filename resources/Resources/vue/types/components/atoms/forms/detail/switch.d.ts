import { Element } from '~/types/components/atoms/forms/detail/index'
import { Text } from '~/types/form/form-v1'

export interface SwitchfieldDtlOptions {
  information?: Text
  readonly?: boolean
  disabled?: boolean
}

type ListenerType = 'change'

export interface SwitchfieldDtl {
  type: 'switchfield'
  options: SwitchfieldDtlOptions
  listener?: (element: Element, type: ListenerType, value: any) => void
  default?: boolean | number
  rules?: Array<string>
}
