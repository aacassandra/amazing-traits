import { FormDetailDefault, FormDetailEditor } from '~/types/form/detail'
import { TextfieldDtl } from '~/types/components/atoms/forms/detail/text'
import { PasswordfieldDtl } from '~/types/components/atoms/forms/detail/password'
import { NumberfieldDtl } from '~/types/components/atoms/forms/detail/number'
import { TextAreafieldDtl } from '~/types/components/atoms/forms/detail/textarea'
import { TextEditorfieldDtl } from '~/types/components/atoms/forms/detail/texteditor'
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
import { RupiahfieldDtl } from '~/types/components/atoms/forms/detail/rupiah'
import { PhonefieldDtl } from '~/types/components/atoms/forms/detail/phone'

export default (fl: FormDetailDefault | FormDetailEditor) => {
  let value = null

  if (fl.type !== 'editor') {
    if (fl.type === 'string') {
      value = ''
    } else if (fl.type === 'number') {
      value = 0
    } else if (fl.type === 'decimal') {
      value = 0.0
    } else if (fl.type === 'date') {
      value = new Date()
    } else if (fl.type === 'percentage') {
      value = 0
    } else if (fl.type === 'boolean') {
      value = true
    } else if (fl.type === 'rupiah') {
      value = '0'
    }
    return value
  }

  const { editor } = fl

  // eslint-disable-next-line @typescript-eslint/ban-ts-comment
  // @ts-ignore
  const indefault = fl.editor.default

  if (
    ['textfield', 'textareafield', 'texteditorfield', 'phonefield'].includes(
      editor.type,
    )
  ) {
    value = indefault !== undefined ? indefault : ''
  } else if (['numberfield', 'rupiahfield'].includes(editor.type)) {
    value = indefault !== undefined ? indefault : 0
  } else if (editor.type === 'selectfield') {
    if (editor.options.multiple) {
      value = []
    } else {
      value = indefault !== undefined ? indefault : ''
    }
  } else if (editor.type === 'popupfield') {
    if (editor.options.multiple) {
      value = []
    } else {
      value = indefault !== undefined ? indefault : ''
    }
  } else if (['subdetailfield', 'tagsfield'].includes(editor.type)) {
    value = []
  } else if (
    ['datetimefield', 'timefield', 'datefield'].includes(editor.type)
  ) {
    value = indefault !== undefined ? indefault : ''
  } else if (['daterangefield', 'datetimerangefield'].includes(editor.type)) {
    value = {
      start: '',
      end: '',
    }
  } else if (editor.type === 'filefield') {
    value = indefault !== undefined ? indefault : null
  } else if (editor.type === 'switchfield' || editor.type === 'checkboxfield') {
    value = indefault !== undefined ? indefault : true
  }

  return value
}
