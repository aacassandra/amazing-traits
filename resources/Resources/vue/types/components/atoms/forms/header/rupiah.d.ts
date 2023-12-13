import { Element } from '~/types/components/atoms/forms/header/index'
import { Text } from '~/types/form/form-v1'

export interface RupiahfieldOptions {
  label: Text
  information?: Text
  inline: boolean
  placeholder?: Text
  readonly?: boolean
  disabled?: boolean
  hidden?: boolean
}

export type ListenerType = string

export interface Rupiahfield {
  type: 'rupiahfield'
  outVal?: null
  value: number
  col: string
  rules?: Array<string>
  options: RupiahfieldOptions
  listener?: (element: Element, type: ListenerType, value: any) => void
  hasChanged?: boolean
  default?: 'inherit' | number
  setValue?(value: any): void
}
