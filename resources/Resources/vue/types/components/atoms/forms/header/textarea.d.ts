import { Element } from '~/types/components/atoms/forms/header/index'
import { Text } from '~/types/form/form-v1'

export interface TextAreafieldOptions {
  label: Text
  information?: Text
  inline: boolean
  placeholder?: Text
  readonly?: boolean
  disabled?: boolean
  hidden?: boolean
}

type ListenerType = 'keyup' | 'keydown' | 'focus' | 'blur' | 'change'

export interface TextAreafield {
  type: 'textareafield'
  outVal?: null
  value: string
  col: string
  rules?: Array<string>
  options: TextAreafieldOptions
  listener?: (element: Element, type: ListenerType, value: any) => void
  hasChanged?: boolean
  default?: 'inherit' | string
  setValue?(value: any): void
}
