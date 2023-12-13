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
          tempValParent[rowIndex].errors &&
          tempValParent[rowIndex].errors(tempValParent, form, rowIndex, field),
      }"
      :step="step"
      :min="min"
      :max="max"
      :placeholder="t(placeholder)"
      :disabled="isDisabled !== undefined ? isDisabled : disabled"
      :readonly="readonly || false"
      @input="methods.onChange"
      @change="methods.onChange"
      @blur="methods.onChange"
      @focus="methods.onChange"
      @keyup="methods.onChange"
      @keydown="methods.onChange"
    />
    <form-validation
      :rows="tempValParent"
      :row-index="rowIndex"
      :field="field"
      :parent-editor="parentEditor"
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
    Ref,
    ref,
    UnwrapRef,
    watch,
  } from 'vue'
  import FormValidation from './Validation.vue'
  import { Element } from '~/types/components/atoms/forms/detail'
  import { ObjectReader, ObjectUpdater, t } from '~/services'
  import { FormSubDetail } from '~/types/form/subdetail'
  import { Lang } from '~/types/form/form-v1'

  defineComponent({
    name: 'TextfieldDetail',
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
    uniqueId: {
      type: String,
      required: true,
    },
    parentEditor: {
      type: Object as () => FormSubDetail,
      required: true,
    },
    rules: {
      type: Array as () => Array<string>,
      default: () => {
        return []
      },
    },
  })

  const form = inject('form') as FormSubDetail
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
      get: () =>
        ObjectReader(tempValParent.value[props.rowIndex], props.field) || 0,
      set: (val) =>
        ObjectUpdater(tempValParent.value[props.rowIndex], props.field, val),
    }),
  )
  const isDisabled = ref(
    computed({
      get: () =>
        ObjectReader(
          tempValParent.value[props.rowIndex],
          `disabled.${props.field}`,
        ),
      set: (val) =>
        ObjectUpdater(
          tempValParent.value[props.rowIndex],
          `disabled.${props.field}`,
          val,
        ),
    }),
  )
  const tempValParent = inject('tempValParent') as Ref<UnwrapRef<Array<any>>>
  const nativeEdit = ref(false)
  if (tempVal.value === undefined || tempVal.value === null) {
    tempVal_.value = props.default
  } else {
    tempVal_.value = tempVal.value
  }

  const elementId = ref(
    `${props.uniqueId}-${props.field}-${props.rowIndex}`.replace('.', '-'),
  )

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
    form.ready++
  })

  watch(tempVal, () => {
    if (tempVal.value && !nativeEdit.value) {
      tempVal_.value = tempVal.value
      methods.onChange({
        type: 'change',
        target: { value: tempVal_.value },
      })
    }
  })
</script>
