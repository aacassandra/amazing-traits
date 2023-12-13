import { Element } from '~/types/components/atoms/forms/detail/index'
import { Text } from '~/types/form/form-v1'
import {
  AddRowFromEmpty,
  AddRowFromPopup,
  FormDetailProperties,
  TableConfigs,
  FormMultiDetailProperties,
} from '~/types/form/detail'
import { ComputedRef } from '@vue/reactivity'

export interface SubfieldDtlOptions {
  title?: string
  readonly?: boolean
  disabled?: boolean
  addRow: AddRowFromEmpty | AddRowFromPopup
  clearAllRow?: {
    active: boolean
    disabled: boolean
    exceptRowIndex?: number
  }
  verivyRow?: {
    className?: string
  }
  tableConfigs?: TableConfigs
  // This feature is useful for displaying multiple tables but with the same 1 api request.
  // With the aim of separating the display of data for example because of the existence of a group or category.
  // when using this, don't forget to use a property of type FormMultiDetailProperties
  multiTable?: {
    active: boolean
    tabs: Array<{
      name?: string
      key: string
      icon?: { value: string; position: 'text-icon' | 'icon-text' }
    }>
  }
  // including value from json response for submitting form [mode edit only]
  // format [column_name_from_addrow_popup]
  // example ['ql_m_kode_belanja_id']
  includes?: Array<string>
  modal?: {
    width: string
  }
  element?: {
    header?: ComputedRef<string>
    footer?: ComputedRef<string>
  }
}

export type ListenerType = string

export type SubfieldDtl = {
  type: 'subdetailfield'
  ready?: number
  properties: FormDetailProperties | FormMultiDetailProperties
  rules?: Array<string>
  options?: SubfieldDtlOptions
  listener?: (element: Element, type: ListenerType, value: any) => void
  focusing?: (element: Element) => boolean
  // is_validate is validator from rules
  verivy?: (element: Element, isValidate: boolean) => boolean
  // rows: Array<any> no need rows like detail, because the value direcly save to detail field value
}
