import { Textfield } from '~/types/components/atoms/forms/header/text'
import { Rupiahfield } from '~/types/components/atoms/forms/header/rupiah'
import { Passwordfield } from '~/types/components/atoms/forms/header/password'
import { TextAreafield } from '~/types/components/atoms/forms/header/textarea'
import { Numberfield } from '~/types/components/atoms/forms/header/number'
import { Selectfield } from '~/types/components/atoms/forms/header/select'
import { Switchfield } from '~/types/components/atoms/forms/header/switch'
import { Datefield } from '~/types/components/atoms/forms/header/date'
import { DateTimefield } from '~/types/components/atoms/forms/header/datetime'
import { Timefield } from '~/types/components/atoms/forms/header/time'
import { Tagsfield } from '~/types/components/atoms/forms/header/tags'
import { Slugfield } from '~/types/components/atoms/forms/header/slug'
import { TextEditorfield } from '~/types/components/atoms/forms/header/texteditor'
import { Popupfield } from '~/types/components/atoms/forms/header/popup'
import { Filefield } from '~/types/components/atoms/forms/header/file'
import { DateRangefield } from '~/types/components/atoms/forms/header/daterange'
import { DateTimeRangefield } from '~/types/components/atoms/forms/header/datetimerange'
import { MonthYearfield } from '~/types/components/atoms/forms/header/monthyear'
import { Phonefield } from '~/types/components/atoms/forms/header/phone'

export type FormHeaderProperties = {
  [key: string]:
    | Textfield
    | Rupiahfield
    | Passwordfield
    | TextAreafield
    | Numberfield
    | Selectfield
    | Switchfield
    | Datefield
    | MonthYearfield
    | DateRangefield
    | DateTimefield
    | DateTimeRangefield
    | Timefield
    | Tagsfield
    | Slugfield
    | TextEditorfield
    | Popupfield
    | Filefield
    | Phonefield
  errors: any
}

export type FormHeader = {
  type: 'header'
  model: string
  key: string
  ready?: number
  docs?: boolean
  properties: FormHeaderProperties
}
