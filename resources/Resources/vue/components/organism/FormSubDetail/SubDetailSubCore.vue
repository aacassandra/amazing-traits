<template>
  <div class="w-full">
    <div v-if="column.type === 'editor' && column.editor.type === 'textfield'">
      <Textfield
        :row-index="rowIndex"
        :field="column.field"
        :placeholder="column.editor.options.placeholder"
        :listener="column.editor.listener"
        :listener-config="column.editor.options.listener"
        :disabled="column.editor.options.disabled || allDisabled"
        :readonly="column.editor.options.readonly"
        :default="column.editor.default"
        :unique-id="methods.onGetRandomUniqueId()"
        :parent-editor="form"
      />
    </div>
    <div
      v-if="column.type === 'editor' && column.editor.type === 'switchfield'"
    >
      <Switchfield
        :row-index="rowIndex"
        :field="column.field"
        :listener="column.editor.listener"
        :disabled="column.editor.options.disabled"
        :readonly="column.editor.options.readonly"
        :default="column.editor.default"
        :unique-id="methods.onGetRandomUniqueId()"
        :parent-editor="form"
      />
    </div>
    <div
      v-if="column.type === 'editor' && column.editor.type === 'checkboxfield'"
    >
      <Checkboxfield
        :row-index="rowIndex"
        :field="column.field"
        :listener="column.editor.listener"
        :disabled="column.editor.options.disabled"
        :readonly="column.editor.options.readonly"
        :default="column.editor.default"
        :unique-id="methods.onGetRandomUniqueId()"
        :parent-editor="form"
      />
    </div>
    <div
      v-if="column.type === 'editor' && column.editor.type === 'rupiahfield'"
    >
      <Rupiahfield
        :row-index="rowIndex"
        :field="column.field"
        :placeholder="column.editor.options.placeholder"
        :listener="column.editor.listener"
        :listener-config="column.editor.options.listener"
        :disabled="column.editor.options.disabled || allDisabled"
        :readonly="column.editor.options.readonly"
        :default="column.editor.default"
        :unique-id="methods.onGetRandomUniqueId()"
        :parent-editor="form"
      />
    </div>
    <div v-if="column.type === 'editor' && column.editor.type === 'phonefield'">
      <Phonefield
        :row-index="rowIndex"
        :field="column.field"
        :placeholder="column.editor.options.placeholder"
        :listener="column.editor.listener"
        :display="column.editor.options.display"
        :output="column.editor.options.output"
        :listener-config="column.editor.options.listener"
        :disabled="column.editor.options.disabled || allDisabled"
        :readonly="column.editor.options.readonly"
        :default="column.editor.default"
        :unique-id="methods.onGetRandomUniqueId()"
        :parent-editor="form"
        :country-code="item.options.countryCode"
      />
    </div>
    <div
      v-if="column.type === 'editor' && column.editor.type === 'passwordfield'"
    >
      <Passwordfield
        :row-index="rowIndex"
        :field="column.field"
        :placeholder="column.editor.options.placeholder"
        :listener="column.editor.listener"
        :disabled="column.editor.options.disabled || allDisabled"
        :readonly="column.editor.options.readonly"
        :default="column.editor.default"
        :unique-id="methods.onGetRandomUniqueId()"
        :parent-editor="form"
      />
    </div>
    <div
      v-if="column.type === 'editor' && column.editor.type === 'numberfield'"
    >
      <Numberfield
        :row-index="rowIndex"
        :field="column.field"
        :placeholder="column.editor.options.placeholder"
        :listener="column.editor.listener"
        :disabled="column.editor.options.disabled || allDisabled"
        :readonly="column.editor.options.readonly"
        :default="column.editor.default"
        :unique-id="methods.onGetRandomUniqueId()"
        :parent-editor="form"
        :rules="column.editor.rules"
      />
    </div>
    <div
      v-if="
        column.type === 'editor' &&
        column.editor.type === 'selectfield' &&
        column.editor.version === 1
      "
    >
      <SelectfieldV1
        :row-index="rowIndex"
        :field="column.field"
        :placeholder="column.editor.options.placeholder"
        :listener="column.editor.listener"
        :disabled="column.editor.options.disabled || allDisabled"
        :readonly="column.editor.options.readonly"
        :default="column.editor.default"
        :items="column.editor.options.items"
        :select-field="column.editor.options.field"
        :override-params="column.editor.options.api?.overrideParams"
        :is-multiple="column.editor.options.multiple"
        :reduce="column.editor.options.reduce"
        :api="column.editor.options.api"
        :unique-id="methods.onGetRandomUniqueId()"
        :parent-editor="form"
      />
    </div>
    <div
      v-if="
        column.type === 'editor' &&
        column.editor.type === 'selectfield' &&
        column.editor.version === 2
      "
    >
      <SelectfieldV2
        :row-index="rowIndex"
        :field="column.field"
        :placeholder="column.editor.options.placeholder"
        :listener="column.editor.listener"
        :disabled="column.editor.options.disabled || allDisabled"
        :disabled-items="column.editor.options.disabledItems"
        :readonly="column.editor.options.readonly"
        :default="column.editor.default"
        :items="column.editor.options.items"
        :select-field="column.editor.options.field"
        :override-params="column.editor.options.api?.overrideParams"
        :is-multiple="column.editor.options.multiple"
        :reduce="column.editor.options.reduce"
        :api="column.editor.options.api"
        :clearable="column.editor.options.clearable"
        :unique-id="methods.onGetRandomUniqueId()"
        :parent-editor="form"
      />
    </div>
    <div v-if="column.type === 'editor' && column.editor.type === 'tagsfield'">
      <Tagsfield
        :row-index="rowIndex"
        :field="column.field"
        :placeholder="column.editor.options.placeholder"
        :listener="column.editor.listener"
        :disabled="column.editor.options.disabled || allDisabled"
        :readonly="column.editor.options.readonly"
        :default="column.editor.default"
        :unique-id="methods.onGetRandomUniqueId()"
        :parent-editor="form"
      />
    </div>
    <div v-if="column.type === 'editor' && column.editor.type === 'listfield'">
      <Listfield
        :row-index="rowIndex"
        :field="column.field"
        :placeholder="column.editor.options.placeholder"
        :listener="column.editor.listener"
        :disabled="column.editor.options.disabled || allDisabled"
        :readonly="column.editor.options.readonly"
        :default="column.editor.default"
        :unique-id="methods.onGetRandomUniqueId()"
        :parent-editor="form"
      />
    </div>
    <div v-if="column.type === 'editor' && column.editor.type === 'filefield'">
      <Filefield
        :row-index="rowIndex"
        :viewer="column.editor.options.viewer"
        :field="column.field"
        :placeholder="column.editor.options.placeholder"
        :listener="column.editor.listener"
        :disabled="column.editor.options.disabled || allDisabled"
        :readonly="column.editor.options.readonly"
        :default="column.editor.default"
        :clearable="column.editor.options.clearable"
        :unique-id="methods.onGetRandomUniqueId()"
        :parent-editor="form"
        :auto-upload="column.editor.options.autoUpload"
        :rules="column.editor.rules"
      />
    </div>
    <div
      v-if="column.type === 'editor' && column.editor.type === 'textareafield'"
    >
      <TextAreafield
        :row-index="rowIndex"
        :field="column.field"
        :placeholder="column.editor.options.placeholder"
        :listener="column.editor.listener"
        :listener-config="column.editor.options.listener"
        :disabled="column.editor.options.disabled || allDisabled"
        :readonly="column.editor.options.readonly"
        :default="column.editor.default"
        :unique-id="methods.onGetRandomUniqueId()"
        :parent-editor="form"
      />
    </div>
    <div v-if="column.type === 'editor' && column.editor.type === 'datefield'">
      <Datefield
        :row-index="rowIndex"
        :field="column.field"
        :placeholder="column.editor.options.placeholder"
        :listener="column.editor.listener"
        :input="column.editor.options.input"
        :output="column.editor.options.output"
        :disabled="column.editor.options.disabled || allDisabled"
        :readonly="column.editor.options.readonly"
        :default="column.editor.default"
        :unique-id="methods.onGetRandomUniqueId()"
        :parent-editor="form"
      />
    </div>
    <div
      v-if="column.type === 'editor' && column.editor.type === 'datetimefield'"
    >
      <DateTimefield
        :row-index="rowIndex"
        :field="column.field"
        :placeholder="column.editor.options.placeholder"
        :listener="column.editor.listener"
        :input="column.editor.options.input"
        :output="column.editor.options.output"
        :disabled="column.editor.options.disabled || allDisabled"
        :readonly="column.editor.options.readonly"
        :default="column.editor.default"
        :unique-id="methods.onGetRandomUniqueId()"
        :parent-editor="form"
        :in24h="column.editor.options.in24h"
      />
    </div>
    <div v-if="column.type === 'editor' && column.editor.type === 'timefield'">
      <Timefield
        :row-index="rowIndex"
        :field="column.field"
        :placeholder="column.editor.options.placeholder"
        :listener="column.editor.listener"
        :input="column.editor.options.input"
        :output="column.editor.options.output"
        :in24h="column.editor.options.in24h"
        :disabled="column.editor.options.disabled || allDisabled"
        :readonly="column.editor.options.readonly"
        :default="column.editor.default"
        :unique-id="methods.onGetRandomUniqueId()"
        :parent-editor="form"
      />
    </div>
    <div
      v-if="column.type === 'editor' && column.editor.type === 'daterangefield'"
    >
      <DateRangefield
        :row-index="rowIndex"
        :field="column.field"
        :placeholder="column.editor.options.placeholder"
        :listener="column.editor.listener"
        :input="column.editor.options.input"
        :output="column.editor.options.output"
        :disabled="column.editor.options.disabled || allDisabled"
        :readonly="column.editor.options.readonly"
        :default="column.editor.default"
        :unique-id="methods.onGetRandomUniqueId()"
        :parent-editor="form"
      />
    </div>
    <div
      v-if="
        column.type === 'editor' && column.editor.type === 'datetimerangefield'
      "
    >
      <DateTimeRangefield
        :row-index="rowIndex"
        :field="column.field"
        :placeholder="column.editor.options.placeholder"
        :listener="column.editor.listener"
        :input="column.editor.options.input"
        :output="column.editor.options.output"
        :disabled="column.editor.options.disabled || allDisabled"
        :readonly="column.editor.options.readonly"
        :in24h="column.editor.options.in24h"
        :default="column.editor.default"
        :unique-id="methods.onGetRandomUniqueId()"
        :parent-editor="form"
      />
    </div>
    <div v-if="column.type === 'editor' && column.editor.type === 'popupfield'">
      <Popupfield
        :row-index="rowIndex"
        :field="column.field"
        :placeholder="column.editor.options.placeholder"
        :listener="column.editor.listener"
        :disabled="column.editor.options.disabled || allDisabled"
        :readonly="column.editor.options.readonly"
        :default="column.editor.default"
        :select-field="column.editor.options.field"
        :override-params="column.editor.options.api?.overrideParams"
        :is-multiple="column.editor.options.multiple"
        :reduce="column.editor.options.reduce"
        :api="column.editor.options.api"
        :clearable="column.editor.options.clearable"
        :unique-id="methods.onGetRandomUniqueId()"
        :parent-editor="form"
        :table-configs="column.editor.options.tableConfigs"
      />
    </div>
    <div
      v-if="column.type === 'editor' && column.editor.type === 'pinpointfield'"
    >
      <Pinpoinfield
        :row-index="rowIndex"
        :field="column.field"
        :placeholder="column.editor.options.placeholder"
        :listener="column.editor.listener"
        :disabled="column.editor.options.disabled || allDisabled"
        :readonly="column.editor.options.readonly"
        :default="column.editor.default"
        :is-multiple="column.editor.options.multiple"
        :clearable="column.editor.options.clearable"
        :unique-id="methods.onGetRandomUniqueId()"
        :parent-editor="form"
      />
    </div>
    <div
      v-if="
        column.type === 'editor' && column.editor.type === 'texteditorfield'
      "
    >
      <TextEditorfield
        :row-index="rowIndex"
        :field="column.field"
        :placeholder="column.editor.options.placeholder"
        :listener="column.editor.listener"
        :disabled="column.editor.options.disabled || allDisabled"
        :readonly="column.editor.options.readonly"
        :default="column.editor.default"
        :clearable="column.editor.options.clearable"
        :unique-id="methods.onGetRandomUniqueId()"
        :parent-editor="form"
      />
    </div>
    <div
      v-if="column.type === 'editor' && column.editor.type === 'subdetailfield'"
    >
      <SubDetailfield
        :row-index="rowIndex"
        :field="column.field"
        :editor="column.editor"
        :parent-editor="form"
        :disabled="column.editor.options.disabled || allDisabled"
      />
    </div>
    <div v-else-if="column.type === 'rupiah'">
      {{ IntToRupiah(formattedRow[column.field], 'Rp. ') }}
    </div>
    <div v-else-if="column.field === 'action'">
      <button
        :disabled="
          allDisabled ||
          form.options.addRow.options?.tableConfigs?.selectAll ||
          ObjectReader(tempVal[rowIndex], `disabled.action`)
        "
        class="btn btn-sm btn-error"
        @click="methods.onRemoveRow"
      >
        <i class="fas fa-trash-alt"></i>
      </button>
    </div>
    <div
      v-else-if="column.type !== 'editor'"
      :style="{
        ...(ObjectReader(tempVal[rowIndex], `style.${column.field}`) || {}),
        fontWeight: ObjectReader(tempVal[rowIndex], `bold.${column.field}`)
          ? 'bold'
          : '',
      }"
      :class="ObjectReader(tempVal[rowIndex], `class.${column.field}`) || ''"
    >
      {{ formattedRow[column.field] }}
    </div>
  </div>
</template>

<script setup lang="ts">
  import { inject, provide, reactive, Ref, UnwrapRef, defineProps } from 'vue'
  import Textfield from '~/components/atoms/form/subdetail/Textfield.vue'
  import Rupiahfield from '~/components/atoms/form/subdetail/Rupiahfield.vue'
  import Passwordfield from '~/components/atoms/form/subdetail/Passwordfield.vue'
  import Numberfield from '~/components/atoms/form/subdetail/Numberfield.vue'
  import Tagsfield from '~/components/atoms/form/subdetail/Tagsfield.vue'
  import Listfield from '~/components/atoms/form/subdetail/Listfield.vue'
  import Filefield from '~/components/atoms/form/subdetail/Filefield.vue'
  import TextAreafield from '~/components/atoms/form/subdetail/TextAreafield.vue'
  import Datefield from '~/components/atoms/form/subdetail/Datefield.vue'
  import DateTimefield from '~/components/atoms/form/subdetail/DateTimefield.vue'
  import Timefield from '~/components/atoms/form/subdetail/Timefield.vue'
  import DateRangefield from '~/components/atoms/form/subdetail/DateRangefield.vue'
  import SelectfieldV1 from '~/components/atoms/form/subdetail/SelectfieldV1.vue'
  import SelectfieldV2 from '~/components/atoms/form/subdetail/SelectfieldV2.vue'
  import Popupfield from '~/components/atoms/form/subdetail/Popupfield/index.vue'
  import Pinpoinfield from '~/components/atoms/form/subdetail/Pinpoinfield/index.vue'
  import TextEditorfield from '~/components/atoms/form/subdetail/TextEditorfield/index.vue'
  import DateTimeRangefield from '~/components/atoms/form/subdetail/DateTimeRangefield.vue'
  import SubDetailfield from '~/components/atoms/form/subdetail/SubDetailfield/index.vue'
  import Phonefield from '~/components/atoms/form/subdetail/Phonefield.vue'
  import Switchfield from '~/components/atoms/form/subdetail/Switchfield.vue'
  import Checkboxfield from '~/components/atoms/form/subdetail/Checkboxfield.vue'

  import { Element } from '~/types/components/atoms/forms/detail'
  import {
    GetRandomString,
    IntToRupiah,
    ObjectReader,
    ObjectUpdater,
  } from '~/services'
  import {
    FormDetailAction,
    FormDetailDefault,
    FormDetailEditor,
  } from '~/types/form/detail'
  import { FormSubDetail } from '~/types/form/subdetail'

  type Column = FormDetailDefault | FormDetailEditor | FormDetailAction
  const props = defineProps({
    rowIndex: {
      type: Number,
      required: true,
    },
    column: {
      type: Object as () => Column,
      required: true,
    },
    formattedRow: {
      type: Object,
      required: true,
    },
    rowId: {
      type: [String, Number],
      default: undefined,
    },
  })

  const allDisabled = inject('allDisabled') as Ref<UnwrapRef<boolean>>
  const totalRow = inject('totalRow') as Ref<UnwrapRef<number>>
  const form = inject('form') as FormSubDetail
  const tempVal = inject('tempValParent') as Ref<UnwrapRef<Array<any>>>
  const methods = {
    onRemoveRow: () => {
      totalRow.value--
      if (props.rowId) {
        tempVal.value.forEach((row, rowIndex) => {
          if (props.rowId && row.id === props.rowId) {
            tempVal.value.splice(rowIndex, 1)
          } else if (rowIndex === props.rowIndex) {
            tempVal.value.splice(rowIndex, 1)
          }
        })
      } else {
        tempVal.value.splice(props.rowIndex, 1)
      }
    },
    onGetRandomUniqueId: () => {
      return GetRandomString(5)
    },
  }

  const element = reactive<Element>({
    rowIndex: props.rowIndex,
    setDisabled: (option) => {
      const rowIndex =
        typeof option.rowIndex === 'number' ? option.rowIndex : props.rowIndex
      ObjectUpdater(
        tempVal.value,
        `${rowIndex}.disabled.${option.field}`,
        option.value,
      )
    },
    setBold: (option) => {
      const rowIndex =
        typeof option.rowIndex === 'number' ? option.rowIndex : props.rowIndex
      if (typeof option.field === 'string') {
        ObjectUpdater(
          tempVal.value,
          `rows.${rowIndex}.bold.${option.field}`,
          option.value,
        )
      } else if (Array.isArray(option.field)) {
        option.field.forEach((field) => {
          ObjectUpdater(
            tempVal.value,
            `rows.${rowIndex}.bold.${field}`,
            option.value,
          )
        })
      }
    },
    setClass: (option) => {
      const rowIndex =
        typeof option.rowIndex === 'number' ? option.rowIndex : props.rowIndex
      if (typeof option.field === 'string') {
        ObjectUpdater(
          tempVal.value,
          `rows.${rowIndex}.class.${option.field}`,
          option.value,
        )
      } else if (Array.isArray(option.field)) {
        option.field.forEach((field) => {
          ObjectUpdater(
            tempVal.value,
            `rows.${rowIndex}.class.${field}`,
            option.value,
          )
        })
      }
    },
    setStyle: (option) => {
      const rowIndex =
        typeof option.rowIndex === 'number' ? option.rowIndex : props.rowIndex
      if (typeof option.field === 'string') {
        ObjectUpdater(
          tempVal.value,
          `rows.${rowIndex}.style.${option.field}`,
          option.value,
        )
      } else if (Array.isArray(option.field)) {
        option.field.forEach((field) => {
          ObjectUpdater(
            tempVal.value,
            `rows.${rowIndex}.style.${field}`,
            option.value,
          )
        })
      }
    },
    getAllRows(): Array<any> {
      return tempVal.value
    },
    getValue: (option) => {
      const rowIndex =
        typeof option.rowIndex === 'number' ? option.rowIndex : props.rowIndex
      return ObjectReader(tempVal.value, `${rowIndex}.${option.field}`)
    },
    getValues: (rowIndex?: number) => {
      const fixIndex = rowIndex || props.rowIndex
      return ObjectReader(tempVal.value, `${fixIndex}`)
    },
    setValue: (option) => {
      const rowIndex =
        typeof option.rowIndex === 'number' ? option.rowIndex : props.rowIndex
      ObjectUpdater(tempVal.value, `${rowIndex}.${option.field}`, option.value)
    },
    setValues: (option) => {
      const rowIndex =
        typeof option.rowIndex === 'number' ? option.rowIndex : props.rowIndex
      Object.entries(option.data).forEach(([key, value]) => {
        ObjectUpdater(tempVal.value, `[${rowIndex}].${key}`, value)
      })
    },
  })

  provide('element', element)
</script>
