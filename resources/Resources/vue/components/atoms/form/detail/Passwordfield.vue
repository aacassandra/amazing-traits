<template>
  <div>
    <input
      :id="elementId"
      v-model="tempVal"
      autocomplete="off"
      type="password"
      :class="{
        'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:outline-none focus:border-blue-500 block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 disabled:bg-gray-200 disabled:text-gray-400 dark:disabled:text-gray-400 dark:disabled:bg-gray-600': true,
        'bg-red-50 border border-red-500 text-red-900  placeholder-red-700 dark:placeholder-red-700 dark:text-red-700 text-sm rounded-lg focus:ring-red-500 focus:outline-none focus:border-red-500 block w-full p-1.5 dark:bg-red-100 dark:border-red-400':
          temp.form.rows[rowIndex].errors &&
          temp.form.rows[rowIndex].errors(temp.form, rowIndex, field),
      }"
      :placeholder="t(placeholder)"
      :disabled="disabled || false"
      :readonly="readonly || false"
      @input="methods.onChange"
      @change="methods.onChange"
      @blur="methods.onChange"
      @focus="methods.onChange"
      @keyup="methods.onChange"
      @keydown="methods.onChange"
    />
    <form-validation
      :rows="temp.form.rows"
      :row-index="rowIndex"
      :field="field"
      :form="temp.form"
    />
  </div>
</template>

<script setup lang="ts">
  import {
    computed,
    defineComponent,
    defineProps,
    inject,
    onMounted,
    ref,
  } from 'vue'
  import FormValidation from './Validation.vue'
  import { FormDetail } from '~/types/form/detail'
  import { Element } from '~/types/components/atoms/forms/detail'
  import { GetRandomString, ObjectReader, ObjectUpdater, t } from "~/services";
  import { Lang } from '~/types/form/form-v1'

  defineComponent({
    name: 'PasswordfieldDetail',
  })

  const props = defineProps({
    rowIndex: {
      type: Number,
      required: true,
    },
    field: {
      type: String,
      required: true,
    },
    placeholder: {
      type: [String, Object as () => Lang],
      default: '',
    },
    listener: {
      type: Function,
      default: null,
    },
    disabled: {
      type: Boolean,
      default: false,
    },
    readonly: {
      type: Boolean,
      default: false,
    },
    default: {
      type: String,
      default: '',
    },
  })

  const temp = inject('temp') as {
    form: FormDetail
  }
  const element = inject('element') as Element
  const tempVal = ref(
    computed({
      get: () => ObjectReader(temp.form.rows[props.rowIndex], props.field),
      set: (val) =>
        ObjectUpdater(temp.form.rows[props.rowIndex], props.field, val),
    }),
  )

  // set default
  if (!tempVal.value) {
    tempVal.value = props.default
  }

  const uid = GetRandomString(5)
  const elementId = ref(
    `${uid}-${props.field}-${props.rowIndex}`.replace('.', '-'),
  )

  const methods = {
    onChange: (evt) => {
      if (props.listener) {
        props.listener(element, evt.type, evt.target.value)
      }
    },
  }

  onMounted(() => {
    temp.form.ready++
  })
</script>
