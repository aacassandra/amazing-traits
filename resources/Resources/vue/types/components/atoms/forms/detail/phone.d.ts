import { Element } from '~/types/components/atoms/forms/detail/index'
import { Text } from '~/types/form/form-v1'

export interface PhonefieldDtlOptions {
  information?: Text
  placeholder?: Text
  readonly?: boolean
  disabled?: boolean
  // +NN-NNNN-NNNN
  display?: string
  output?: string
  countryCode?: number
}

type ListenerType = 'keyup' | 'keydown' | 'focus' | 'blur' | 'change'

export interface PhonefieldDtl {
  type: 'phonefield'
  rules?: Array<string>
  options?: PhonefieldDtlOptions
  listener?: (element: Element, type: ListenerType, value: any) => void
  default?: number | string
}
