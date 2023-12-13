import { Element } from '~/types/components/atoms/forms/header/index'
import { Text } from '~/types/form/form-v1'

export interface NumberfieldOptions {
  label: Text
  information?: Text
  inline: boolean
  placeholder?: Text
  readonly?: boolean
  disabled?: boolean
  hidden?: boolean
}

type ListenerType =
  | 'keyup'
  | 'keydown'
  | 'focus'
  | 'blur'
  | 'change'
  | 'mounted'

export interface Numberfield {
  type: 'numberfield'
  outVal?: null
  value: number | string
  col: string
  rules?: Array<string>
  options: NumberfieldOptions
  listener?: (element: Element, type: ListenerType, value: any) => void
  hasChanged?: boolean
  default?: 'inherit' | number | string
  setValue?(value: any): void
}
