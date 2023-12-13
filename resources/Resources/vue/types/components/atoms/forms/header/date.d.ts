import { Element } from '~/types/components/atoms/forms/header/index'
import { Text } from '~/types/form/form-v1'

export interface DatefieldOptions {
  label: Text
  information?: Text
  inline: boolean
  placeholder?: Text
  input?: string
  output?: string
  readonly?: boolean
  disabled?: boolean
  hidden?: boolean
}

type ListenerType =
  | 'change'
  | 'opened'
  | 'closed'
  | 'updated'
  | 'month-change'
  | 'year-change'

export interface Datefield {
  type: 'datefield'
  outVal?: null
  value: string
  col: string
  rules?: Array<string>
  options: DatefieldOptions
  listener?: (element: Element, type: ListenerType, value: any) => void
  hasChanged?: boolean
  default?: string
  setValue?(value: any): void
}
