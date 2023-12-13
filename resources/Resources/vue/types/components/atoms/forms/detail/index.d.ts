export interface Form {
  version: number
  schema: {
    [key: string]: any
    errors: any
  }
}

export interface OptionGetValue {
  rowIndex?: number
  field: string
}

export interface OptionDisabled {
  rowIndex?: number
  field: string | Array<string>
  value: boolean
}

export interface OptionBold {
  rowIndex?: number
  field: string | Array<string>
  value: boolean
}

export interface OptionClass {
  rowIndex?: number
  field: string | Array<string>
  value: string
}

export interface OptionStyle {
  rowIndex?: number
  field: string | Array<string>
  value: {
    [key: string]: any
  }
}

export interface OptionSetValue {
  rowIndex?: number
  field: string | Array<string>
  value: any
}

export interface OptionSetValues {
  rowIndex?: number
  data: {
    [key: string]: any
  }
}

export interface Element {
  rowIndex: number

  /**
   * example:
   * element.setDisabled({
   *   rowIndex: 0, [optional, if empty, then the current rowIndex is used]
   *   field: 'textarea',
   *   value: true/false
   * })
   *
   * @param option
   */
  setDisabled(option: OptionDisabled): void

  /**
   * NOTE: Only work with column type <'string' | 'number' | 'decimal' | 'date' | 'percentage' | 'boolean'>
   * example:
   * element.setBold({
   *   rowIndex: 0, [optional, if empty, then the current rowIndex is used]
   *   field: 'textarea',
   *   value: true/false
   * })
   *
   * @param option
   */
  setBold(option: OptionBold): void

  /**
   * NOTE: Only work with column type <'string' | 'number' | 'decimal' | 'date' | 'percentage' | 'boolean'>
   * example:
   * element.setClass({
   *   rowIndex: 0, [optional, if empty, then the current rowIndex is used]
   *   field: 'textarea',
   *   value: 'text-green-300'
   * })
   *
   * @param option
   */
  setClass(option: OptionClass): void

  /**
   * NOTE: Only work with column type <'string' | 'number' | 'decimal' | 'date' | 'percentage' | 'boolean'>
   * example:
   * element.setStyle({
   *   rowIndex: 0, [optional, if empty, then the current rowIndex is used]
   *   field: 'textarea',
   *   style: {
   *     textAlign: 'center',
   *     color: 'red',
   *     fontSize: '12px',
   *     fontFamily: 'Poppins400'
   *     ...
   *   }
   * })
   *
   * @param option
   */
  setStyle(option: OptionStyle): void

  /**
   * example:
   * element.getValue({
   *   rowIndex: 0, [optional, if empty, then the current rowIndex is used]
   *   field: 'textarea'
   * })
   *
   * @param option
   */
  getValue(option: OptionGetValue): any

  /**
   * example:
   * element.getValues(rowIndex = 0)
   * note: rowIndex is optional, if empty, then the current rowIndex is used
   *
   * @param rowIndex
   */
  getValues(rowIndex?: number): any

  getAllRows(): Array<any>
  /**
   * example:
   * element.setValue({
   *   rowIndex: 0, [optional, if empty, then the current rowIndex is used]
   *   field: 'textarea',
   *   value: 'i need update this value'
   * })
   *
   * @param option
   */
  setValue(option: OptionSetValue): void

  /**
   * example:
   * element.setValues({
   *   rowIndex: 0, [optional, if empty, then the current rowIndex is used]
   *   data: {
   *       name: 'title',
   *       age: 2,
   *       address: 'xxx'
   *   }
   * })
   *
   * @param option
   */
  setValues(option: OptionSetValues): void
}

export interface Element {
  [key: string]: any
}

export interface OldParams {
  [key: string]: any
}

export interface Disabled {
  general: boolean
  detail?: boolean
  create?: boolean
  edit?: boolean
}

export type ApiInterface = {
  url?: string
  model?: string
  parameters?: {
    [key: string]: any
  },
  transform?(row: any): void
  cache?: boolean
  overrideParams?(element: Element, oldParams: OldParams): OldParams
  root?: string
}

export type ApiInterfaceAddRowFormPopup = {
  url?: string
  model?: string
  parameters?: {
    [key: string]: any
  },
  transform?(row: any): void
  cache?: boolean
  overrideParams?(oldParams: OldParams): OldParams
  root?: string
}

export type Mode = 'general' | 'detail' | 'edit' | 'create'
