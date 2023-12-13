import { Element } from '~/types/components/atoms/forms/detail/index'
import { Text } from '~/types/form/form-v1'

export interface DateTimeRangefieldDtlOptions {
  information?: Text
  placeholder?: Text
  output?: string
  in24h?: boolean
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

export interface Default {
  start: 'now' | 'inherit' | ''
  end: 'now' | 'tomorrow' | 'inherit' | ''
}

export interface DateTimeRangefieldDtl {
  type: 'datetimerangefield'
  rules?: Array<string>
  options: DateTimeRangefieldDtlOptions
  listener?: (element: Element, type: ListenerType, value: any) => void
  default?: Default
}
