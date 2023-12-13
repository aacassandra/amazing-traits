import { Element } from '~/types/components/atoms/forms/detail/index'
import { Text } from '~/types/form/form-v1'

export interface DateRangefieldDtlOptions {
  information?: Text
  placeholder?: Text
  output?: string
  readonly?: boolean
  disabled?: boolean
  getFrom?: {
    start: string
    end: string
  }
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

export interface DateRangefieldDtl {
  type: 'daterangefield'
  rules?: Array<string>
  options: DateRangefieldDtlOptions
  listener?: (element: Element, type: ListenerType, value: any) => void
  default?: Default
}
