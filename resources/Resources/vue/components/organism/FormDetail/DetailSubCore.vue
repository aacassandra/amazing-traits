<template>
  <div class="w-full">
    <div v-if="column.type === 'editor' && column.editor.type === 'textfield'">
      <Textfield
        v-if="column.type === 'editor' && column.editor.type === 'textfield'"
        :row-index="rowIndex"
        :field="column.field"
        :placeholder="column.editor.options.placeholder"
        :listener="column.editor.listener"
        :disabled="column.editor.options.disabled"
        :readonly="column.editor.options.readonly"
        :default="column.editor.default"
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
        :disabled="column.editor.options.disabled"
        :readonly="column.editor.options.readonly"
        :default="column.editor.default"
      />
    </div>
    <div v-if="column.type === 'editor' && column.editor.type === 'phonefield'">
      <Phonefield
        :row-index="rowIndex"
        :field="column.field"
        :display="column.editor.options.display"
        :output="column.editor.options.output"
        :placeholder="column.editor.options.placeholder"
        :listener="column.editor.listener"
        :listener-config="column.editor.options.listener"
        :disabled="column.editor.options.disabled"
        :readonly="column.editor.options.readonly"
        :default="column.editor.default"
        :country-code="column.editor.options.countryCode"
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
        :disabled="column.editor.options.disabled"
        :readonly="column.editor.options.readonly"
        :default="column.editor.default"
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
        :disabled="column.editor.options.disabled"
        :readonly="column.editor.options.readonly"
        :default="column.editor.default"
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
        :disabled="column.editor.options.disabled"
        :listener-config="column.editor.options.listener"
        :readonly="column.editor.options.readonly"
        :default="column.editor.default"
      />
    </div>
    <div v-if="column.type === 'editor' && column.editor.type === 'tagsfield'">
      <Tagsfield
        :row-index="rowIndex"
        :field="column.field"
        :placeholder="column.editor.options.placeholder"
        :listener="column.editor.listener"
        :disabled="column.editor.options.disabled"
        :readonly="column.editor.options.readonly"
        :default="column.editor.default"
      />
    </div>
    <div v-if="column.type === 'editor' && column.editor.type === 'listfield'">
      <Listfield
        :row-index="rowIndex"
        :field="column.field"
        :placeholder="column.editor.options.placeholder"
        :listener="column.editor.listener"
        :disabled="column.editor.options.disabled"
        :readonly="column.editor.options.readonly"
        :default="column.editor.default"
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
        :disabled="column.editor.options.disabled"
        :readonly="column.editor.options.readonly"
        :default="column.editor.default"
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
        :disabled="column.editor.options.disabled"
        :readonly="column.editor.options.readonly"
        :default="column.editor.default"
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
        :disabled="column.editor.options.disabled"
        :readonly="column.editor.options.readonly"
        :default="column.editor.default"
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
        :disabled="column.editor.options.disabled"
        :readonly="column.editor.options.readonly"
        :default="column.editor.default"
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
        :disabled="column.editor.options.disabled"
        :readonly="column.editor.options.readonly"
        :in24h="column.editor.options.in24h"
        :default="column.editor.default"
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
        :disabled="column.editor.options.disabled"
        :readonly="column.editor.options.readonly"
        :default="column.editor.default"
        :items="column.editor.options.items"
        :select-field="column.editor.options.field"
        :override-params="column.editor.options.api?.overrideParams"
        :is-multiple="column.editor.options.multiple"
        :reduce="column.editor.options.reduce"
        :api="column.editor.options.api"
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
        :disabled="column.editor.options.disabled"
        :readonly="column.editor.options.readonly"
        :default="column.editor.default"
        :items="column.editor.options.items"
        :disabled-items="column.editor.options.disabledItems"
        :select-field="column.editor.options.field"
        :override-params="column.editor.options.api?.overrideParams"
        :is-multiple="column.editor.options.multiple"
        :reduce="column.editor.options.reduce"
        :api="column.editor.options.api"
        :clearable="column.editor.options.clearable"
      />
    </div>
    <div v-if="column.type === 'editor' && column.editor.type === 'popupfield'">
      <Popupfield
        :row-index="rowIndex"
        :field="column.field"
        :placeholder="column.editor.options.placeholder"
        :listener="column.editor.listener"
        :disabled="column.editor.options.disabled"
        :readonly="column.editor.options.readonly"
        :default="column.editor.default"
        :select-field="column.editor.options.field"
        :override-params="column.editor.options.api?.overrideParams"
        :is-multiple="column.editor.options.multiple"
        :reduce="column.editor.options.reduce"
        :api="column.editor.options.api"
        :clearable="column.editor.options.clearable"
        :table-configs="column.editor.options.tableConfigs"
      />
    </div>
    <div
      v-if="column.type === 'editor' && column.editor.type === 'pinpointfield'"
    >
      <Pinpointfield
        :row-index="rowIndex"
        :field="column.field"
        :placeholder="column.editor.options.placeholder"
        :listener="column.editor.listener"
        :disabled="column.editor.options.disabled"
        :readonly="column.editor.options.readonly"
        :default="column.editor.default"
        :is-multiple="column.editor.options.multiple"
        :clearable="column.editor.options.clearable"
      />
    </div>
    <div v-if="column.type === 'editor' && column.editor.type === 'filefield'">
      <Filefield
        :row-index="rowIndex"
        :viewer="column.editor.options.viewer"
        :field="column.field"
        :placeholder="column.editor.options.placeholder"
        :listener="column.editor.listener"
        :disabled="column.editor.options.disabled"
        :readonly="column.editor.options.readonly"
        :default="column.editor.default"
        :clearable="column.editor.options.clearable"
        :auto-upload="column.editor.options.autoUpload"
        :rules="column.editor.rules"
      />
    </div>
    <div
      v-if="
        column.type === 'editor' &&
        column.editor.type === 'texteditorfield' &&
        column.editor.version === 1
      "
    >
      <TextEditorfieldV1
        :row-index="rowIndex"
        :field="column.field"
        :placeholder="column.editor.options.placeholder"
        :listener="column.editor.listener"
        :disabled="column.editor.options.disabled"
        :readonly="column.editor.options.readonly"
        :default="column.editor.default"
        :clearable="column.editor.options.clearable"
      />
    </div>
    <div
      v-if="
        column.type === 'editor' &&
        column.editor.type === 'texteditorfield' &&
        column.editor.version === 2
      "
    >
      <TextEditorfieldV2
        :row-index="rowIndex"
        :field="column.field"
        :placeholder="column.editor.options.placeholder"
        :listener="column.editor.listener"
        :disabled="column.editor.options.disabled"
        :readonly="column.editor.options.readonly"
        :default="column.editor.default"
        :clearable="column.editor.options.clearable"
      />
    </div>
    <div v-if="column.type === 'image'">
      <img
        v-if="formattedRow[column.field]"
        :src="formattedRow[column.field]"
        alt=""
        :style="column.style"
      />
      <img
        v-else-if="temp.form.rows[rowIndex][column.fieldText]"
        :src="`https://avatar.oxro.io/avatar.svg?name=${
          temp.form.rows[rowIndex][column.fieldText]
        }&background=73bf94&color=000&length=1`"
        :style="column.style"
        alt=""
      />
      <div v-else>-</div>
    </div>
    <div v-else-if="props.column.templates">
      <div>
        <span
          v-html="
            GetColumnTemplate(
              props,
              props.formattedRow[props.column.field] !== null
                ? props.formattedRow[props.column.field]
                : null,
            )
          "
        ></span>
      </div>
    </div>
    <div
      v-else-if="
        column.type === 'editor' && column.editor.type === 'subdetailfield'
      "
    >
      <SubDetailfield
        :row-index="rowIndex"
        :field="column.field"
        :editor="column.editor"
      />
    </div>
    <div v-else-if="column.type === 'action'">
      <button
        v-if="column.actions.removeRow && column.actions.removeRow.active"
        :disabled="
          (temp.form.addRow.from === 'popup' &&
            temp.form.addRow.options?.tableConfigs?.selectAll) ||
          column.actions.removeRow.disabled ||
          ObjectReader(temp.form.rows[rowIndex], `disabled.action`)
        "
        class="btn btn-sm btn-error text-white px-2.5"
        @click="column.actions.removeRow.onRemoveRow(rowIndex, rowId)"
      >
        <i class="fas fa-trash-alt"></i>
      </button>
    </div>
    <div v-else-if="column.type === 'rupiah'">
      {{ IntToRupiah(formattedRow[column.field], 'Rp. ') }}
    </div>
    <div
      v-else-if="column.type !== 'editor' && column.type !== 'action'"
      :style="{
        ...(ObjectReader(temp.form.rows[rowIndex], `style.${column.field}`) ||
          {}),
        fontWeight: ObjectReader(
          temp.form.rows[rowIndex],
          `bold.${column.field}`,
        )
          ? 'bold'
          : '',
      }"
      :class="
        ObjectReader(temp.form.rows[rowIndex], `class.${column.field}`) || ''
      "
    >
      {{ formattedRow[column.field] }}
    </div>
  </div>
</template>

<script setup lang="ts">
  import { inject, provide, reactive, defineProps } from 'vue'
  import Textfield from '~/components/atoms/form/detail/Textfield.vue'
  import Rupiahfield from '~/components/atoms/form/detail/Rupiahfield.vue'
  import Passwordfield from '~/components/atoms/form/detail/Passwordfield.vue'
  import Numberfield from '~/components/atoms/form/detail/Numberfield.vue'
  import TextAreafield from '~/components/atoms/form/detail/TextAreafield.vue'
  import Tagsfield from '~/components/atoms/form/detail/Tagsfield.vue'
  import Listfield from '~/components/atoms/form/detail/Listfield.vue'
  import Datefield from '~/components/atoms/form/detail/Datefield.vue'
  import DateTimefield from '~/components/atoms/form/detail/DateTimefield.vue'
  import Timefield from '~/components/atoms/form/detail/Timefield.vue'
  import DateRangefield from '~/components/atoms/form/detail/DateRangefield.vue'
  import DateTimeRangefield from '~/components/atoms/form/detail/DateTimeRangefield.vue'
  import SelectfieldV1 from '~/components/atoms/form/detail/SelectfieldV1.vue'
  import SelectfieldV2 from '~/components/atoms/form/detail/SelectfieldV2.vue'
  import Popupfield from '~/components/atoms/form/detail/Popupfield/index.vue'
  import Pinpointfield from '~/components/atoms/form/detail/Pinpoinfield/index.vue'
  import Filefield from '~/components/atoms/form/detail/Filefield.vue'
  import TextEditorfieldV1 from '~/components/atoms/form/detail/TextEditorfield/v1/index.vue'
  import TextEditorfieldV2 from '~/components/atoms/form/detail/TextEditorfield/v2/index.vue'
  import SubDetailfield from '~/components/atoms/form/detail/SubDetailfield/index.vue'
  import Phonefield from '~/components/atoms/form/detail/Phonefield.vue'
  import Switchfield from '~/components/atoms/form/detail/Switchfield.vue'
  import Checkboxfield from '~/components/atoms/form/detail/Checkboxfield.vue'

  import { IntToRupiah, ObjectReader, ObjectUpdater } from '~/services'
  import { Element } from '~/types/components/atoms/forms/detail'
  import {
    // ColumnProperties,
    FormDetail,
  } from '~/types/form/detail'
  import GetColumnTemplate from '~/helpers/table/GetColumnTemplate'

  const props = defineProps({
    rowIndex: {
      type: Number,
      required: true,
    },
    column: {
      type: Object as () => any,
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

  const temp = inject('temp') as {
    form: FormDetail
  }

  const element = reactive<Element>({
    rowIndex: props.rowIndex,
    setDisabled: (option) => {
      const rowIndex =
        typeof option.rowIndex === 'number' ? option.rowIndex : props.rowIndex
      if (typeof option.field === 'string') {
        ObjectUpdater(
          temp.form,
          `rows.${rowIndex}.disabled.${option.field}`,
          option.value,
        )
      } else if (Array.isArray(option.field)) {
        option.field.forEach((field) => {
          ObjectUpdater(
            temp.form,
            `rows.${rowIndex}.disabled.${field}`,
            option.value,
          )
        })
      }
    },
    setBold: (option) => {
      const rowIndex =
        typeof option.rowIndex === 'number' ? option.rowIndex : props.rowIndex
      if (typeof option.field === 'string') {
        ObjectUpdater(
          temp.form,
          `rows.${rowIndex}.bold.${option.field}`,
          option.value,
        )
      } else if (Array.isArray(option.field)) {
        option.field.forEach((field) => {
          ObjectUpdater(
            temp.form,
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
          temp.form,
          `rows.${rowIndex}.class.${option.field}`,
          option.value,
        )
      } else if (Array.isArray(option.field)) {
        option.field.forEach((field) => {
          ObjectUpdater(
            temp.form,
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
          temp.form,
          `rows.${rowIndex}.style.${option.field}`,
          option.value,
        )
      } else if (Array.isArray(option.field)) {
        option.field.forEach((field) => {
          ObjectUpdater(
            temp.form,
            `rows.${rowIndex}.style.${field}`,
            option.value,
          )
        })
      }
    },
    getValue: (option) => {
      const rowIndex =
        typeof option.rowIndex === 'number' ? option.rowIndex : props.rowIndex
      return ObjectReader(temp.form, `rows.${rowIndex}.${option.field}`)
    },
    getValues: (rowIndex?: number) => {
      const fixIndex = rowIndex || props.rowIndex
      return ObjectReader(temp.form, `rows.${fixIndex}`)
    },
    getAllRows: () => {
      return temp.form.rows
    },
    setValue: (option) => {
      const rowIndex =
        typeof option.rowIndex === 'number' ? option.rowIndex : props.rowIndex
      if (typeof option.field === 'string') {
        ObjectUpdater(
          temp.form,
          `rows.${rowIndex}.${option.field}`,
          option.value,
        )
      } else if (Array.isArray(option.field)) {
        option.field.forEach((field) => {
          ObjectUpdater(temp.form, `rows.${rowIndex}.${field}`, option.value)
        })
      }
    },
    setValues: (option) => {
      const rowIndex =
        typeof option.rowIndex === 'number' ? option.rowIndex : props.rowIndex
      Object.entries(option.data).forEach(([key, value]) => {
        ObjectUpdater(temp.form, `rows.[${rowIndex}].${key}`, value)
      })
    },
  })

  provide('element', element)
</script>
