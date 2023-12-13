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
          temp.form.rows[rowIndex].errors &&
          temp.form.rows[rowIndex].errors(temp.form, rowIndex, field),
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
    reactive,
    ref,
    watch,
  } from 'vue'
  import FormValidation from './Validation.vue'
  import { FormDetail } from '~/types/form/detail'
  import { Element } from '~/types/components/atoms/forms/detail'
  import { GetRandomString, ObjectReader, ObjectUpdater, t } from "~/services";
  import { RupiahListenerConfig } from '~/types/components/atoms/forms/detail/rupiah'
  import PhoneFormatter from '~/services/PhoneFormatter'
  import { Lang } from '~/types/form/form-v1'

  defineComponent({
    name: 'RupiahfieldDetail',
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
      default: 62,
    },
  })

  const temp = inject('temp') as {
    form: FormDetail
  }
  const element = inject('element') as Element
  const nativeEdit = ref(false)
  const tempVal = ref(
    computed({
      get: () => ObjectReader(temp.form.rows[props.rowIndex], props.field),
      set: (val) =>
        ObjectUpdater(temp.form.rows[props.rowIndex], props.field, val),
    }),
  )
  const tempVal_ = reactive({
    value_: null,
  })

  // [TODO] isDisabled untuk element.setDisabled() perlu di implementasikan ke semua form detail
  // [TODO] @input perlu di hapus, karena bikin lemot
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

  const uid = GetRandomString(5)
  const elementId = ref(
    `${uid}-${props.field}-${props.rowIndex}`.replace('.', '-'),
  )
  const onBlur = ref(false)
  const methods = {
    onChange: (evt) => {
      // [TODO] listener config perlu di apply di semua komponent, ini hanyd di rupiah [detail]
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
    temp.form.ready++
    methods.onChange({ type: 'mounted', target: { value: tempVal.value } })
  })

  watch(tempVal, () => {
    if (temp.form.rows[props.rowIndex] && !nativeEdit.value) {
      if (onBlur.value) {
        tempVal_.value_ = PhoneFormatter(
          tempVal.value,
          props.display,
          props.countryCode,
        )
      } else {
        tempVal_.value_ = tempVal.value.replace(/([^\w]+|\s+)/g, '')
      }
      methods.onChange({ type: 'change', target: { value: tempVal.value } })
    }
  })
</script>
