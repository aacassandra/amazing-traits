import { Element } from '~/types/components/atoms/forms/header/index'
import { Text } from '~/types/form/form-v1'
export interface TagsfieldOptions {
  label: Text
  information?: Text
  inline: boolean
  placeholder?: Text
  readonly?: boolean
  disabled?: boolean
  hidden?: boolean
}

type ListenerType =
  | 'add'
  | 'remove'
  | 'input'
  | 'edit'
  | 'invalid'
  | 'click'
  | 'focus'
  | 'blur'
  | 'change'

export interface Tagsfield {
  type: 'tagsfield'
  outVal?: Array<any>
  value: Array<any>
  col: string
  rules?: Array<string>
  options: TagsfieldOptions
  listener?: (element: Element, type: ListenerType, value: any) => void
  hasChanged?: boolean
  default?: 'inherit' | Array<any>
  setValue?(value: any): void
}
