import { Element } from '~/types/components/atoms/forms/detail/index'
import { Text } from '~/types/form/form-v1'

export interface NumberfieldDtlOptions {
  information?: Text
  placeholder?: Text
  readonly?: boolean
  disabled?: boolean
}

type ListenerType = 'keyup' | 'keydown' | 'focus' | 'blur' | 'change' | 'init'

export interface NumberfieldDtl {
  type: 'numberfield'
  rules?: Array<string>
  options?: NumberfieldDtlOptions
  listener?: (element: Element, type: ListenerType, value: any) => void
  default?: number | string
}
