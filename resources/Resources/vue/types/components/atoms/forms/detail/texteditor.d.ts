import { Text } from '~/types/form/form-v1'
import { Element } from '~/types/components/atoms/forms/header'
import { toolbarDef } from '~/types/components/atoms/forms/header/texteditor/v1'

// Text Edtitor v1
export interface TextEditorfieldDtlOptionsV1 {
  toolbars?: toolbarDef
  information?: Text
  placeholder?: Text
  readonly?: boolean
  disabled?: boolean
  clearable?: boolean
}

type ListenerTypeV1 =
  | 'focus'
  | 'blur'
  | 'change'
  | 'keyup'
  | 'keydown'
  | 'keypress'

export interface TextEditorfieldDtlV1 {
  type: 'texteditorfield'
  version: number
  options?: TextEditorfieldDtlOptionsV1
  listener?: (element: Element, type: ListenerTypeV1, value: any) => void
  rules?: Array<string>
  default?: 'inherit' | string
}

// End of Text Edtitor v1

// Text Edtitor v2
export interface TextEditorfieldDtlOptionsV2 {
  information?: Text
  placeholder?: Text
  readonly?: boolean
  disabled?: boolean
  clearable?: boolean
}

type ListenerTypeV2 =
  | 'focus'
  | 'blur'
  | 'change'
  | 'keyup'
  | 'keydown'
  | 'keypress'

export interface TextEditorfieldDtlV2 {
  type: 'texteditorfield'
  version: number
  options?: TextEditorfieldDtlOptionsV2
  listener?: (element: Element, type: ListenerTypeV2, value: any) => void
  rules?: Array<string>
  default?: 'inherit' | string
}
// End of Text Edtitor v1

export type TextEditorfieldDtl = TextEditorfieldDtlV1 | TextEditorfieldDtlV2
