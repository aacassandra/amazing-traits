import { Text } from '~/types/form/form-v1'
import { Element } from '~/types/components/atoms/forms/header'

export type ListenerType =
  | 'keyup'
  | 'keydown'
  | 'focus'
  | 'blur'
  | 'change'
  | 'image-link'
  | 'enter'
  | 'paste'

export interface TextEditorfieldOptionsV2 {
  label: Text
  information?: Text
  inline: boolean
  placeholder?: Text
  disabled?: boolean
  readonly?: boolean
  hidden?: boolean
}

export interface TextEditorfieldV2 {
  type: 'texteditorfield'
  version: 2
  outVal?: null
  value: string
  col: string
  rules?: Array<string>
  options: TextEditorfieldOptionsV2
  listener?: (element: Element, type: ListenerType, value: any) => void
  hasChanged?: boolean
  default?: 'inherit' | string
  setValue?(value: any): void
  getValue?(field: string): any
}
