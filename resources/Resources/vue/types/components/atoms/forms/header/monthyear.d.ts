import { Element } from '~/types/components/atoms/forms/header/index'
import { Text } from '~/types/form/form-v1'

export interface MonthYearfieldOptions {
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

export interface MonthYearfield {
  type: 'monthyearfield'
  outVal?: null
  value: string
  col: string
  rules?: Array<string>
  options: MonthYearfieldOptions
  listener?: (element: Element, type: ListenerType, value: any) => void
  hasChanged?: boolean
  default?: string
  setValue?(value: any): void
}
