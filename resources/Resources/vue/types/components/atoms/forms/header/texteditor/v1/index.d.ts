import { Text } from '~/types/form/form-v1'
import { Element } from '~/types/components/atoms/forms/header'

type toolbarFontNameOptions = string
type toolbarStyleGroupOptions =
  | 'style'
  | 'bold'
  | 'italic'
  | 'underline'
  | 'clear'
type toolbarFontGroupOptions =
  | 'fontname'
  | 'fontsize'
  | 'fontsizeunit'
  | 'color'
  | 'forecolor'
  | 'backcolor'
  | 'bold'
  | 'italic'
  | 'underline'
  | 'strikethrough'
  | 'superscript'
  | 'subscript'
  | 'clear'
type toolbarFontsizeGroupOptions = 'fontsize' | 'fontname' | 'color'
type toolbarColorGroupOptions = 'color'
type toolbarParaGroupOptions = 'ul' | 'ol' | 'paragraph' | 'style' | 'height'
type toolbarHeightGroupOptions = 'height'
type toolbarTableGroupOptions = 'table'
type toolbarInsertGroupOptions = 'link' | 'picture' | 'hr' | 'table' | 'video'
type toolbarViewGroupOptions = 'fullscreen' | 'codeview' | 'help'
type toolbarHelpGroupOptions = 'help'
type miscGroupOptions = 'fullscreen' | 'codeview' | 'undo' | 'redo' | 'help'

export type toolbarDef = Array<
  | ['style', toolbarStyleGroupOptions[]]
  | ['font', toolbarFontGroupOptions[]]
  | ['fontname', toolbarFontNameOptions[]]
  | ['fontsize', toolbarFontsizeGroupOptions[]]
  | ['color', toolbarColorGroupOptions[]]
  | ['para', toolbarParaGroupOptions[]]
  | ['height', toolbarHeightGroupOptions[]]
  | ['table', toolbarTableGroupOptions[]]
  | ['insert', toolbarInsertGroupOptions[]]
  | ['view', toolbarViewGroupOptions[]]
  | ['help', toolbarHelpGroupOptions[]]
  | ['misc', miscGroupOptions[]]
>

export type ListenerType =
  | 'keyup'
  | 'keydown'
  | 'focus'
  | 'blur'
  | 'change'
  | 'image-link'
  | 'enter'
  | 'paste'

export interface TextEditorfieldOptionsV1 {
  label: Text
  information?: Text
  inline: boolean
  placeholder?: Text
  toolbars?: toolbarDef
  disabled?: boolean
  readonly?: boolean
  hidden?: boolean
}

export interface TextEditorfieldV1 {
  type: 'texteditorfield'
  version: 1
  outVal?: null
  value: string
  col: string
  rules?: Array<string>
  options: TextEditorfieldOptionsV1
  listener?: (element: Element, type: ListenerType, value: any) => void
  hasChanged?: boolean
  default?: 'inherit' | string
  setValue?(value: any): void
  getValue?(field: string): any
}
