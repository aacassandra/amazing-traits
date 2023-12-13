import { Element } from '~/types/components/atoms/forms/header/index'
import { Text } from '~/types/form/form-v1'

export interface SlugfieldOptions {
  label: Text
  information?: Text
  inline: boolean
  placeholder?: Text
  readonly?: boolean
  disabled?: boolean
  hidden?: boolean
}

type ListenerType = 'keyup' | 'keydown' | 'focus' | 'blur' | 'change'

export interface Slugfield {
  type: 'slugfield'
  outVal?: null
  value: string
  col: string
  rules?: Array<string>
  options: SlugfieldOptions
  listener?: (element: Element, type: ListenerType, value: any) => void
  hasChanged?: boolean
  default?: 'inherit' | string
  setValue?(value: any): void
}
