<template>
  <div>
    <input
      :id="elementId"
      autocomplete="off"
      type="text"
      :class="{
        [`flatpickr-${elementId}-date`]: true,
        'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:outline-none focus:border-blue-500 block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 disabled:bg-gray-200 disabled:text-gray-400 dark:disabled:text-gray-400 dark:disabled:bg-gray-600': true,
        'bg-red-50 border border-red-500 text-red-900  placeholder-red-700 dark:placeholder-red-700 dark:text-red-700 text-sm rounded-lg focus:ring-red-500 focus:outline-none focus:border-red-500 block w-full p-1.5 dark:bg-red-100 dark:border-red-400':
          temp.form.rows[rowIndex].errors &&
          temp.form.rows[rowIndex].errors(temp.form, rowIndex, field),
      }"
      :placeholder="t(placeholder)"
      :disabled="disabled || false"
      :readonly="readonly || false"
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
  import { Flatpickr, GetRandomString, ObjectReader, ObjectUpdater, t } from "~/services";
  import moment from 'moment'
  import { Lang } from '~/types/form/form-v1'

  defineComponent({
    name: 'DateTimefieldDetail',
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
    input: {
      type: String,
      default: 'YYYY-MM-DD HH:mm',
    },
    output: {
      type: String,
      default: 'YYYY-MM-DD HH:mm',
    },
    default: {
      type: String as () => 'inherit' | 'now' | '',
      default: '',
    },
    in24h: {
      type: Boolean,
      default: false,
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
  const tempVal_ = ref()
  const uid = GetRandomString(5)
  const elementId = ref(
    `${uid}-${props.field}-${props.rowIndex}`.replace('.', '-'),
  )

  const nativeEdit = ref(false)
  const isReady = ref(false)
  const init = ref()

  const methods = {
    onChange: (evt) => {
      if (isReady.value) {
        if (props.listener) {
          props.listener(element, evt.type, tempVal.value)
        }
      }
    },
  }

  onMounted(() => {
    tempVal_.value =
      props.default === 'inherit'
        ? moment(tempVal.value || new Date(), props.input).format(props.output)
        : props.default === 'now'
        ? moment(new Date(), props.input).format(props.output)
        : props.default

    setTimeout(() => {
      init.value = Flatpickr(`.flatpickr-${elementId.value}-date`, {
        enableTime: true,
        time_24hr: props.in24h,
        // https://flatpickr.js.org/formatting/
        dateFormat: 'd-m-Y H:i',
        defaultDate: tempVal_.value,
        onDestroy() {
          init.value.destroy()
          methods.onChange({ type: 'destroy' })
        },
        onReady() {
          temp.form.ready++
          setTimeout(() => {
            isReady.value = true
          }, 500)
          methods.onChange({ type: 'ready' })
        },
        onChange(_, dateStr) {
          nativeEdit.value = true
          tempVal.value = moment(dateStr, props.output).format(props.input)
          setTimeout(() => {
            methods.onChange({ type: 'change' })
          }, 200)
          setTimeout(() => {
            nativeEdit.value = false
          }, 100)
        },
        onOpen() {
          methods.onChange({ type: 'opened' })
        },
        onClose() {
          methods.onChange({ type: 'closed' })
        },
        onMonthChange() {
          methods.onChange({ type: 'month-change' })
        },
        onYearChange() {
          methods.onChange({ type: 'year-change' })
        },
        onValueUpdate() {
          methods.onChange({ type: 'updated' })
        },
      })
    }, 500)
  })

  let isUpdating = false
  watch(tempVal, () => {
    if (isReady.value && temp.form.rows[props.rowIndex] && !nativeEdit.value) {
      if (!isUpdating) {
        isUpdating = true
        tempVal_.value = tempVal.value
          ? moment(tempVal.value, props.input).format(props.output)
          : ''
        setTimeout(() => {
          init.value.setDate(tempVal_.value)
          setTimeout(() => {
            isUpdating = false
          }, 100)
        }, 100)
      }
    }
  })
</script>
