import { Element } from '~/types/components/atoms/forms/detail/index'
import { Text } from '~/types/form/form-v1'

export interface TagsfieldDtlOptions {
  placeholder?: Text
  readonly?: boolean
  disabled?: boolean
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

export interface TagsfieldDtl {
  type: 'tagsfield'
  rules?: Array<string>
  options: TagsfieldDtlOptions
  listener?: (element: Element, type: ListenerType, value: any) => void
  default?: Array<any>
}
