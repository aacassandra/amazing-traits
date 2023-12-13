import { Element } from '~/types/components/atoms/forms/header/index'
import { Text } from '~/types/form/form-v1'

export interface FilefieldOptions {
  label: Text
  information?: Text
  inline: boolean
  placeholder?: Text
  disabled?: boolean
  viewer: FileViewerType
  clearable?: boolean
  hidden?: boolean
  autoUpload?: boolean
}

type ListenerType = 'change'

export type FileViewerType = 'image' | 'pdf' | 'none'

export interface Filefield {
  type: 'filefield'
  outVal?: null
  value: string
  linkPreview?: string
  linkResponse?: string
  col: string
  rules?: Array<string>
  options: FilefieldOptions
  listener?: (element: Element, type: ListenerType, value: any) => void
  onchange?: boolean
  hasChanged?: boolean
  default?: string
  setValue?(value: any): void
}
