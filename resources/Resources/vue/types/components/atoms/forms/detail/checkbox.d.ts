import { Element } from '~/types/components/atoms/forms/detail/index'
import { Text } from '~/types/form/form-v1'

export interface CheckboxfieldDtlOptions {
  information?: Text
  readonly?: boolean
  disabled?: boolean
}

type ListenerType = 'change'

export interface CheckboxfieldDtl {
  type: 'checkboxfield'
  options: CheckboxfieldDtlOptions
  listener?: (element: Element, type: ListenerType, value: any) => void
  default?: boolean | number
  rules?: Array<string>
}
