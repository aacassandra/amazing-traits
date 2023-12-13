import { Element } from '~/types/components/atoms/forms/header/index'
import { Text } from '~/types/form/form-v1'

export interface PhoneFieldOptions {
  label: Text
  information?: Text
  inline: boolean
  placeholder?: Text
  readonly?: boolean
  disabled?: boolean
  hidden?: boolean
  // +NN-NNNN-NNNN
  display?: string
  output?: string
  countryCode?: number
}

type ListenerType = 'keyup' | 'keydown' | 'focus' | 'blur' | 'change'

export interface Phonefield {
  type: 'phonefield'
  outVal?: null
  value: string
  default?: 'inherit' | string
  col: string
  rules?: Array<string>
  options: PhoneFieldOptions
  listener?: (element: Element, type: ListenerType, value: any) => void
  hasChanged?: boolean
  setValue?(value: any): void
}
