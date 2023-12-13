<template>
  <div>
    <input
      :id="elementId"
      v-model="tempVal_.value_"
      autocomplete="off"
      type="text"
      :class="{
        'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:outline-none focus:border-blue-500 block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 disabled:bg-gray-200 disabled:text-gray-400 dark:disabled:text-gray-400 dark:disabled:bg-gray-600': true,
        'bg-red-50 border border-red-500 text-red-900  placeholder-red-700 dark:placeholder-red-700 dark:text-red-700 text-sm rounded-lg focus:ring-red-500 focus:outline-none focus:border-red-500 block w-full p-1.5 dark:bg-red-100 dark:border-red-400':
          tempValParent[rowIndex].errors &&
          tempValParent[rowIndex].errors(tempValParent, form, rowIndex, field),
      }"
      :placeholder="t(placeholder)"
      :disabled="isDisabled !== undefined ? isDisabled : disabled"
      :readonly="readonly || false"
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
    reactive,
    ref,
    Ref,
    UnwrapRef,
    watch,
  } from 'vue'
  import FormValidation from './Validation.vue'
  import { Element } from '~/types/components/atoms/forms/detail'
  import { ObjectReader, ObjectUpdater, t } from '~/services'
  import { FormSubDetail } from '~/types/form/subdetail'
  import { RupiahListenerConfig } from '~/types/components/atoms/forms/detail/rupiah'
  import PhoneFormatter from '~/services/PhoneFormatter'
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
      type: [String, Number],
      default: '',
    },
    uniqueId: {
      type: String,
      required: true,
    },
    parentEditor: {
      type: Object as () => FormSubDetail,
      required: true,
    },
    listenerConfig: {
      type: Object as () => RupiahListenerConfig,
      default: () => {
        return {
          exclude: [],
        }
      },
    },
    display: {
      type: String,
      default: '+NN-NNNN-NNNN',
    },
    output: {
      type: String,
      default: 'NNNNNNNNNN',
    },
    countryCode: {
      type: Number,
      default: null,
    },
  })

  const form = inject('form') as FormSubDetail
  const element = inject('element') as Element
  const tempVal = ref(
    computed({
      get: () =>
        ObjectReader(tempValParent.value[props.rowIndex], props.field) || 0,
      set: (val) =>
        ObjectUpdater(tempValParent.value[props.rowIndex], props.field, val),
    }),
  )
  const tempVal_ = reactive({
    value_: null,
  })
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

  const elementId = ref(
    `${props.uniqueId}-${props.field}-${props.rowIndex}`.replace('.', '-'),
  )
  const onBlur = ref(false)
  const methods = {
    onChange: (evt) => {
      if (props.listenerConfig && props.listenerConfig.exclude) {
        if (props.listenerConfig.exclude.includes(evt.type)) {
          return false
        }
      }

      nativeEdit.value = true
      if (evt.type === 'blur') {
        onBlur.value = true
        tempVal_.value_ = PhoneFormatter(
          evt.target.value,
          props.display,
          props.countryCode,
        )
      } else if (evt.type === 'focus') {
        onBlur.value = false
        tempVal_.value_ = evt.target.value.replace(/([^\w]+|\s+)/g, '')
        if (props.countryCode) {
          tempVal_.value_ = tempVal_.value_.replace(props.countryCode, 0)
        }
      }

      tempVal.value = PhoneFormatter(
        evt.target.value,
        props.output,
        props.countryCode,
        false,
      )

      if (props.listener) {
        props.listener(element, evt.type, tempVal.value)
      }
      setTimeout(() => {
        nativeEdit.value = false
      }, 100)
    },
  }

  if (props.default) {
    // set default value
    if (tempVal.value) {
      tempVal_.value_ = PhoneFormatter(
        tempVal.value,
        props.display,
        props.countryCode,
      )
    } else {
      tempVal_.value_ = PhoneFormatter(
        props.default,
        props.display,
        props.countryCode,
      )
    }
  } else {
    if (tempVal.value) {
      tempVal_.value_ = PhoneFormatter(
        tempVal.value,
        props.display,
        props.countryCode,
      )
    } else {
      tempVal_.value_ = ''
    }
  }

  methods.onChange({ type: 'created', target: { value: tempVal.value } })
  onMounted(() => {
    form.ready++
    methods.onChange({ type: 'mounted', target: { value: tempVal.value } })
  })

  watch(tempVal, () => {
    if (!nativeEdit.value && tempValParent.value[props.rowIndex]) {
      if (onBlur.value) {
        tempVal_.value_ = PhoneFormatter(
          tempVal.value,
          props.display,
          props.countryCode,
        )
      } else {
        tempVal_.value_ = tempVal.value.replace(/([^\w]+|\s+)/g, '')
      }
    }
  })
</script>
