import { Element } from '~/types/components/atoms/forms/header/index'
import { Text } from '~/types/form/form-v1'

export interface PasswordEye {
  active: boolean
  style: 'default' | 'click-only'
}

export interface PasswordfieldOptions {
  label: Text
  information?: Text
  inline: boolean
  placeholder?: Text
  readonly?: boolean
  disabled?: boolean
  hidden?: boolean
  eye?: PasswordEye
}

export type ListenerType = string

export interface Passwordfield {
  type: 'passwordfield'
  outVal?: null
  value: string
  col: string
  rules?: Array<string>
  options: PasswordfieldOptions
  listener?: (element: Element, type: ListenerType, value: any) => void
  hasChanged?: boolean
  default?: 'inherit' | string
  setValue?(value: any): void
}
