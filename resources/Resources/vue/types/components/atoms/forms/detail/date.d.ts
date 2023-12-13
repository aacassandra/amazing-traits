import { Element } from '~/types/components/atoms/forms/detail/index'
import { Text } from '~/types/form/form-v1'

export interface DatefieldDtlOptions {
  information?: Text
  placeholder?: Text
  output?: string
  readonly?: boolean
  disabled?: boolean
}

type ListenerType =
  | 'change'
  | 'opened'
  | 'closed'
  | 'updated'
  | 'month-change'
  | 'year-change'

export interface DatefieldDtl {
  type: 'datefield'
  rules?: Array<string>
  options: DatefieldDtlOptions
  listener?: (element: Element, type: ListenerType, value: any) => void
  default?: 'now' | 'inherit' | ''
}
