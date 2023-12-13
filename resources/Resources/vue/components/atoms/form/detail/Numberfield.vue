<template>
  <div>
    <input
      :id="elementId"
      v-model="tempVal_"
      autocomplete="off"
      type="number"
      :class="{
        'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:outline-none focus:border-blue-500 block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 disabled:bg-gray-200 disabled:text-gray-400 dark:disabled:text-gray-400 dark:disabled:bg-gray-600': true,
        'bg-red-50 border border-red-500 text-red-900  placeholder-red-700 dark:placeholder-red-700 dark:text-red-700 text-sm rounded-lg focus:ring-red-500 focus:outline-none focus:border-red-500 block w-full p-1.5 dark:bg-red-100 dark:border-red-400':
          temp.form.rows[rowIndex].errors &&
          temp.form.rows[rowIndex].errors(temp.form, rowIndex, field),
      }"
      :step="step"
      :min="min"
      :max="max"
      :placeholder="t(placeholder)"
      :disabled="disabled || isDisabled"
      :readonly="readonly"
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
    watch,
  } from 'vue'
  import FormValidation from './Validation.vue'
  import { FormDetail } from '~/types/form/detail'
  import { Element } from '~/types/components/atoms/forms/detail'
  import { GetRandomString, ObjectReader, ObjectUpdater, t } from "~/services";
  import { Lang } from '~/types/form/form-v1'

  defineComponent({
    name: 'NumberfieldDetail',
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
      type: [Number, String],
      default: null,
    },
    rules: {
      type: Array as () => Array<string>,
      default: () => {
        return []
      },
    },
  })

  const temp = inject('temp') as {
    form: FormDetail
  }
  const element = inject('element') as Element
  const step = ref(null)
  const min = ref(null)
  const max = ref(null)
  const decimalDigits = ref(0)
  if (props.rules && props.rules.length) {
    props.rules.forEach((rule) => {
      if (rule.includes('decimal')) {
        step.value = '.'
        const split = rule.split(':')
        decimalDigits.value = parseInt(split[1])
        for (let i = 0; i < decimalDigits.value; i++) {
          if (i === decimalDigits.value - 1) {
            step.value += '1'
          } else {
            step.value += '0'
          }
        }
      } else if (rule.includes('min')) {
        min.value = rule.split(':')[1]
      } else if (rule.includes('max')) {
        max.value = rule.split(':')[1]
      }
    })
  }

  const tempVal_ = ref(null)
  const tempVal = ref(
    computed({
      get: () => ObjectReader(temp.form.rows[props.rowIndex], props.field),
      set: (val) =>
        ObjectUpdater(temp.form.rows[props.rowIndex], props.field, val),
    }),
  )

  const isDisabled = ref(
    computed({
      get: () =>
        ObjectReader(temp.form.rows[props.rowIndex], `disabled.${props.field}`),
      set: (val) =>
        ObjectUpdater(
          temp.form.rows[props.rowIndex],
          `disabled.${props.field}`,
          val,
        ),
    }),
  )

  // set default
  if (tempVal.value === undefined || tempVal.value === null) {
    tempVal_.value = props.default
  } else {
    tempVal_.value = tempVal.value
  }

  const uid = GetRandomString(5)
  const elementId = ref(
    `${uid}-${props.field}-${props.rowIndex}`.replace('.', '-'),
  )

  const nativeEdit = ref(false)
  const methods = {
    onChange: (evt) => {
      nativeEdit.value = true
      if (typeof evt.target.value === 'string') {
        if (evt.target.value.includes('.')) {
          if (decimalDigits.value) {
            tempVal.value = parseFloat(evt.target.value).toFixed(
              decimalDigits.value,
            )
          } else {
            tempVal.value = parseFloat(evt.target.value)
          }
        } else {
          tempVal.value = parseInt(evt.target.value)
        }
      } else {
        tempVal.value = evt.target.value
      }

      if (props.listener) {
        props.listener(element, evt.type, tempVal.value)
      }

      setTimeout(() => {
        nativeEdit.value = false
      }, 100)
    },
  }

  onMounted(() => {
    methods.onChange({
      type: 'init',
      target: { value: tempVal_.value },
    })
    temp.form.ready++
  })

  watch(temp, () => {
    if (temp.form.rows[props.rowIndex] && !nativeEdit.value) {
      tempVal_.value = ObjectReader(temp.form.rows[props.rowIndex], props.field)
      methods.onChange({
        type: 'change',
        target: { value: tempVal_.value },
      })
    }
  })
</script>
