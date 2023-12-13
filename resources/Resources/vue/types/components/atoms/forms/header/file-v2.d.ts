import {
  Disabled,
  Element,
  Mode,
} from '~/types/components/atoms/forms/header/index'

export interface FilefieldOptionsV2 {
  label: Text
  information?: Text
  inline: boolean
  disabled?: boolean
  hidden?: boolean
  helpText?: string
  preview: boolean
  onlyPreview: boolean
  edit: {
    force: boolean
    cropper: {
      aspectRatio: number
    }
    enabled: boolean
  }
}

type ListenerType = 'change'

export interface FilefieldV2 {
  type: 'filefield'
  version: 2
  outVal?: null
  value: any
  col: string
  rules?: Array<string>
  options?: FilefieldOptionsV2
  listener?: (element: Element, type: ListenerType, value: any) => void
  imagePreview?: any
  removeFile?: any
  hasChanged?: boolean
  default?: string
  setValue?(value: any): void
}
