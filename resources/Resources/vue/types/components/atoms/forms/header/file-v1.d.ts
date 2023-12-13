import { Element } from '~/types/components/atoms/forms/header/index'
import { Text } from '~/types/form/form-v1'

export interface FilefieldOptionsV1 {
  label: Text
  information?: Text
  inline: boolean
  disabled?: boolean
  hidden?: boolean
  helpText?: string
  preview: boolean
}

type ListenerType = 'change'

export interface FilefieldV1 {
  type: 'filefield'
  version: 1
  outVal?: null
  value: string
  col: string
  rules?: Array<string>
  options: FilefieldOptionsV1
  listener?: (element: Element, type: ListenerType, value: any) => void
  imagePreview?: any
  onchange?: boolean
  hasChanged?: boolean
  default?: string
  setValue?(value: any): void
}
