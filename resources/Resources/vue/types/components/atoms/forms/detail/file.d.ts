import { Element } from '~/types/components/atoms/forms/detail/index'
import { Text } from '~/types/form/form-v1'
//
export interface FilefieldDtlOptions {
  information?: Text
  placeholder?: Text
  readonly?: boolean
  disabled?: boolean
  viewer: FileViewerType
  clearable?: boolean
  autoUpload?: boolean
}

export type ListenerType = string

export type FileViewerType = 'image' | 'pdf' | 'none'

export interface FilefieldDtl {
  type: 'filefield'
  rules?: Array<string>
  options?: FilefieldDtlOptions
  listener?: (element: Element, type: ListenerType, value: any) => void
  default?: 'inherit' | string
}
