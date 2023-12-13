import { Element } from '~/types/components/atoms/forms/detail/index'
import { Text } from '~/types/form/form-v1'

export interface SlugfieldOptions {
  information?: Text
  placeholder?: Text
  readonly?: boolean
  disabled?: boolean
}

type ListenerType = 'keyup' | 'keydown' | 'focus' | 'blur' | 'change'

export interface Slugfield {
  type: 'slugfield'
  rules?: Array<string>
  options: SlugfieldOptions
  listener?: (element: Element, type: ListenerType, value: any) => void
  default?: 'inherit' | string
}
