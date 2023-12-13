import { Element } from '~/types/components/atoms/forms/header/index'
import { Text } from '~/types/form/form-v1'

export interface TextfieldOptions {
  label: Text
  information?: Text
  inline: boolean
  placeholder?: Text
  readonly?: boolean
  disabled?: boolean
  hidden?: boolean
}

export type ListenerType =
  | 'keyup'
  | 'keydown'
  | 'focus'
  | 'blur'
  | 'change'
  | 'mounted'

export interface Textfield {
  type: 'textfield'
  outVal?: null
  value: string
  col: string
  rules?: Array<string>
  options: TextfieldOptions
  listener?: (element: Element, type: ListenerType, value: any) => void
  hasChanged?: boolean
  default?: 'inherit' | string
  setValue?(value: any): void
}
