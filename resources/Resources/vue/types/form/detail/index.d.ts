import { TextfieldDtl } from '~/types/components/atoms/forms/detail/text'
import { PasswordfieldDtl } from '~/types/components/atoms/forms/detail/password'
import { NumberfieldDtl } from '~/types/components/atoms/forms/detail/number'
import { TextAreafieldDtl } from '~/types/components/atoms/forms/detail/textarea'
import { TextEditorfieldDtl } from '~/types/components/atoms/forms/detail/texteditor'
import { SelectfieldDtl } from '~/types/components/atoms/forms/detail/select'
import { PopupfieldDtl } from '~/types/components/atoms/forms/detail/popup'
import { PinpointfieldDtl } from '~/types/components/atoms/forms/detail/pinpoint'
import { SwitchfieldDtl } from '~/types/components/atoms/forms/detail/switch'
import { CheckboxfieldDtl } from '~/types/components/atoms/forms/detail/checkbox'
import { TagsfieldDtl } from '~/types/components/atoms/forms/detail/tags'
import { ListfieldDtl } from '~/types/components/atoms/forms/detail/list'
import { SubfieldDtl } from '~/types/components/atoms/forms/detail/subdetail'
import { DatefieldDtl } from '~/types/components/atoms/forms/detail/date'
import { DateTimefieldDtl } from '~/types/components/atoms/forms/detail/datetime'
import { TimefieldDtl } from '~/types/components/atoms/forms/detail/time'
import { DateRangefieldDtl } from '~/types/components/atoms/forms/detail/daterange'
import { DateTimeRangefieldDtl } from '~/types/components/atoms/forms/detail/datetimerange'
import { FilefieldDtl } from '~/types/components/atoms/forms/detail/file'
import { ApiInterfaceAddRowFormPopup } from '~/types/components/atoms/forms/detail'
import { TableColumns } from '~/types/form/form-v1'
import { RupiahfieldDtl } from '~/types/components/atoms/forms/detail/rupiah'
import { PhonefieldDtl } from '~/types/components/atoms/forms/detail/phone'

export interface FormDetailDefault {
  field?: string
  type:
    | 'string'
    | 'number'
    | 'decimal'
    | 'date'
    | 'percentage'
    | 'boolean'
    | 'rupiah'
  width?: string
  from?: string
  label?: string
  sortable?: boolean
  templates?: string | Array<string>
  filter?: boolean
  filterOptions?: {
    enabled: boolean
    placeholder?: string
    trigger?: 'enter'
  }
  thClass?: string
  tdClass?: string
  dateInputFormat?: string // expects 2018-03-16
  dateOutputFormat?: string
}

export interface FormDetailImage {
  field?: string
  fieldText?: string
  type: 'image'
  style?: any
  width?: string
  label?: string
  sortable?: boolean
  thClass?: string
  tdClass?: string
}

export interface FormDetailAction {
  field?: string
  type: 'action'
  width?: string
  from?: string
  label?: string
  thClass?: string
  tdClass?: string
  sortable?: boolean
  actions?: {
    removeRow?: {
      active: boolean
      disabled?: boolean
      onRemoveRow(index: number): void
    }
  }
}

// don't change this to 'type'. becuse the DetailSubCore.vue types not work when using 'type'
export interface FormDetailEditor {
  field?: string
  type: 'editor'
  width?: string
  from?: string
  label?: string
  thClass?: string
  tdClass?: string
  sortable?: boolean
  editor:
    | TextfieldDtl
    | RupiahfieldDtl
    | PhonefieldDtl
    | PasswordfieldDtl
    | NumberfieldDtl
    | TextAreafieldDtl
    | TextEditorfieldDtl
    | SelectfieldDtl
    | PopupfieldDtl
    | PinpointfieldDtl
    | SwitchfieldDtl
    | CheckboxfieldDtl
    | TagsfieldDtl
    | ListfieldDtl
    | SubfieldDtl
    | DatefieldDtl
    | DateTimefieldDtl
    | TimefieldDtl
    | DateRangefieldDtl
    | DateTimeRangefieldDtl
    | FilefieldDtl
}

export type ColumnProperties =
  | FormDetailDefault
  | FormDetailImage
  | FormDetailAction
  | FormDetailEditor
export type FormDetailProperties = Array<ColumnProperties>
export type FormMultiDetailProperties = {
  [key: string]: FormDetailProperties
}

export interface TableConfigs {
  fixedHeader: boolean
  maxHeight?: string
  lineNumbers?: boolean
}

export type AddRowFromEmpty = {
  from: 'empty'
  active: boolean
  className?: string
  disabled: boolean
  max?: number
}

export interface AddRowFromPopupOptions {
  className?: {
    addNewList?: string
    verivy?: string
  }
  tableConfigs?: {
    fixedHeader?: boolean
    maxHeight?: string
    selectAll?: boolean // active when is
    lineNumbers?: boolean
  }

  isMultiple: boolean
  globalSearch?: boolean

  columns: TableColumns
  uniqueColumn: string

  // this is used for custom id column in model
  model?: string // this feature has removed

  // format ['get_from_column_name:store_at_field_name']
  // example: includes: ['id:ql_m_kode_belanja_id','...']
  // or you can single string when column_name equal to target field name
  // example: includes: ['id:ql_m_kode_belanja_id','coa_parent_id']
  includes?: Array<string>
  api: ApiInterfaceAddRowFormPopup
}

export type AddRowFromPopup = {
  from: 'popup'
  active: boolean
  disabled: boolean
  // conditional, you can return false to cancel this action
  onClick?: () => boolean
  onVerified?: () => void
  options?: AddRowFromPopupOptions
}

export type FormDetail = {
  type: 'detail'
  relation: {
    key: string
    model: string
    column: string
  }
  ready?: number
  addRow: AddRowFromEmpty | AddRowFromPopup
  clearAllRow: {
    active: boolean
    disabled: boolean
    exceptRowIndex?: number
  }
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

  properties: FormDetailProperties | FormMultiDetailProperties
  tableConfigs?: TableConfigs

  // including value from json response for submitting form [mode edit only]
  // format [column_name_from_addrow_popup]
  // example ['ql_m_kode_belanja_id']
  includes?: Array<string>

  // transform are used when edit / preview mode for manipulating json key
  // format ['column_target:get_from_respon_field_name']
  // example: includes: ['kode_belanja:ql_m_kode_belanja.kode_belanja','...']
  transform?: Array<string>
  rows: Array<any>
  listener?(
    type: 'add-new-row' | 'clear-all-row',
    item: any,
    index: number,
  ): void
}
