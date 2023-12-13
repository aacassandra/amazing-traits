import { Element } from '~/types/components/atoms/forms/header/index'
import { Text } from '~/types/form/form-v1'

export interface TimefieldOptions {
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

export interface Timefield {
  type: 'timefield'
  outVal?: null
  value: string
  col: string
  rules?: Array<string>
  options: TimefieldOptions
  listener?: (element: Element, type: ListenerType, value: any) => void
  hasChanged?: boolean
  default?: string
  setValue?(value: any): void
}
