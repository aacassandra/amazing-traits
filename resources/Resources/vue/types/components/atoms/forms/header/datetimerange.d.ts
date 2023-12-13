import { Element } from '~/types/components/atoms/forms/header/index'
import { Text } from '~/types/form/form-v1'

export interface DateTimeRangefieldOptions {
  label: Text
  information?: Text
  inline: boolean
  placeholder?: Text
  input?: string
  output?: string
  in24h?: boolean
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

export interface Value {
  start: string
  end: string
}
export interface Default {
  start: 'now' | 'inherit' | ''
  end: 'now' | 'tomorrow' | 'inherit' | ''
}

export interface DateTimeRangefield {
  type: 'datetimerangefield'
  outVal?: Value
  value: Value
  col: string
  rules?: Array<string>
  options: DateTimeRangefieldOptions
  listener?: (element: Element, type: ListenerType, value: any) => void
  hasChanged?: boolean
  default?: Default
  setValue?(value: any): void
}
