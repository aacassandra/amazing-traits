import { Element } from '~/types/components/atoms/forms/detail/index'
import { Text } from '~/types/form/form-v1'

export interface TimefieldDtlOptions {
  information?: Text
  placeholder?: Text
  output?: string
  in24h?: boolean
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

export interface TimefieldDtl {
  type: 'timefield'
  rules?: Array<string>
  options: TimefieldDtlOptions
  listener?: (element: Element, type: ListenerType, value: any) => void
  default?: 'now' | 'inherit' | ''
}
