import { Element } from '~/types/components/atoms/forms/detail/index'
import { Text } from '~/types/form/form-v1'

export interface RupiahListenerConfig {
  exclude: Array<
    'change' | 'blur' | 'focus' | 'keydown' | 'keyup' | 'mounted' | 'created'
  >
}

export interface RupiahfieldDtlOptions {
  information?: Text
  placeholder?: Text
  readonly?: boolean
  disabled?: boolean
  listener?: RupiahListenerConfig
}

export type ListenerType =
  | 'change'
  | 'blur'
  | 'focus'
  | 'keydown'
  | 'keyup'
  | 'mounted'
  | 'created'

export interface RupiahfieldDtl {
  type: 'rupiahfield'
  rules?: Array<string>
  options?: RupiahfieldDtlOptions
  listener?: (element: Element, type: ListenerType, value: any) => void
  default?: number
}
