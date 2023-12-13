import { Element } from '~/types/components/atoms/forms/header/index'
import { Text } from '~/types/form/form-v1'
export interface SwitchfieldOptions {
  label: Text
  information?: Text
  inline: boolean
  disabled?: boolean
  hidden?: boolean
}

type ListenerType = 'change'

export interface Switchfield {
  type: 'switchfield'
  outVal?: boolean | number
  value: boolean | number
  col: string
  options: SwitchfieldOptions
  rules?: any // ignore this, cause a switch component not require rules
  listener?: (element: Element, type: ListenerType, value: any) => void
  hasChanged?: boolean
  default?: boolean | number
  setValue?(value: any): void
}
