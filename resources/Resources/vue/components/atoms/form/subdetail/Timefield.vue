<template>
  <div>
    <input
      :id="elementId"
      v-model="tempVal"
      autocomplete="off"
      type="text"
      :class="{
        [`flatpickr-${elementId}-date`]: true,
        'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:outline-none focus:border-blue-500 block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 disabled:bg-gray-200 disabled:text-gray-400 dark:disabled:text-gray-400 dark:disabled:bg-gray-600': true,
        'bg-red-50 border border-red-500 text-red-900  placeholder-red-700 dark:placeholder-red-700 dark:text-red-700 text-sm rounded-lg focus:ring-red-500 focus:outline-none focus:border-red-500 block w-full p-1.5 dark:bg-red-100 dark:border-red-400':
          tempValParent[rowIndex].errors &&
          tempValParent[rowIndex].errors(tempValParent, form, rowIndex, field),
      }"
      :placeholder="t(placeholder)"
      :disabled="disabled || false"
      :readonly="readonly || false"
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
    ref,
    Ref,
    UnwrapRef,
    watch,
  } from 'vue'
  import FormValidation from './Validation.vue'
  import { Element } from '~/types/components/atoms/forms/detail'
  import { Flatpickr, ObjectReader, ObjectUpdater, t } from '~/services'
  import { FormSubDetail } from '~/types/form/subdetail'
  import moment from 'moment'
  import { Lang } from '~/types/form/form-v1'

  defineComponent({
    name: 'TimefieldDetail',
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
      default: 'HH:mm',
    },
    output: {
      type: String,
      default: 'HH:mm',
    },
    default: {
      type: String as () => 'inherit' | 'now' | '',
      default: '',
    },
    in24h: {
      type: Boolean,
      default: false,
    },
    uniqueId: {
      type: String,
      required: true,
    },
    parentEditor: {
      type: Object as () => FormSubDetail,
      required: true,
    },
  })

  const form = inject('form') as FormSubDetail
  const element = inject('element') as Element
  const tempVal = ref(
    computed({
      get: () =>
        ObjectReader(tempValParent.value[props.rowIndex], props.field) || '',
      set: (val) =>
        ObjectUpdater(tempValParent.value[props.rowIndex], props.field, val),
    }),
  )
  const tempVal_ = ref()
  const tempValParent = inject('tempValParent') as Ref<UnwrapRef<Array<any>>>

  const elementId = ref(
    `${props.uniqueId}-${props.field}-${props.rowIndex}`.replace('.', '-'),
  )

  const nativeEdit = ref(false)
  const isReady = ref(false)
  const init = ref()
  const modalLock = inject('modalLock') as Ref<UnwrapRef<boolean>>
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
        noCalendar: true,
        time_24hr: props.in24h,
        // https://flatpickr.js.org/formatting/
        dateFormat: 'H:i',
        defaultDate: tempVal_.value,
        onDestroy() {
          init.value.destroy()
          methods.onChange({ type: 'destroy' })
        },
        onReady() {
          form.ready++
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
          modalLock.value = true
          methods.onChange({ type: 'opened' })
        },
        onClose() {
          modalLock.value = false
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
  watch(tempValParent, () => {
    if (
      isReady.value &&
      tempValParent.value[props.rowIndex] &&
      !nativeEdit.value
    ) {
      if (!isUpdating) {
        isUpdating = true
        const newVal = ObjectReader(
          tempValParent.value[props.rowIndex],
          props.field,
        )
        tempVal_.value = newVal
          ? moment(newVal, props.input).format(props.output)
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
