<template>
  <div>
    <input
      :id="elementId"
      v-model="tempVal_"
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
    Ref,
    ref,
    UnwrapRef,
    watch,
  } from 'vue'
  import FormValidation from './Validation.vue'
  import { Element } from '~/types/components/atoms/forms/detail'
  import {
    IntToRupiah,
    ObjectReader,
    ObjectUpdater,
    RupiahFixValue,
    RupiahToInt,
    t,
  } from '~/services'
  import { FormSubDetail } from '~/types/form/subdetail'
  import { RupiahListenerConfig } from '~/types/components/atoms/forms/detail/rupiah'
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
      default: undefined,
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
      type: Number,
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
    listenerConfig: {
      type: Object as () => RupiahListenerConfig,
      default: () => {
        return {
          exclude: [],
        }
      },
    },
  })

  const form = inject('form') as FormSubDetail
  const element = inject('element') as Element
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

  // set default
  if (!tempVal.value) {
    tempVal_.value = props.default
      ? IntToRupiah(props.default.toString(), 'Rp. ')
      : IntToRupiah('0', 'Rp. ')
    tempVal.value = props.default
  } else {
    tempVal_.value = IntToRupiah(tempVal.value.toString(), 'Rp. ')
  }

  const elementId = ref(
    `${props.uniqueId}-${props.field}-${props.rowIndex}`.replace('.', '-'),
  )

  const methods = {
    onChange: (evt) => {
      nativeEdit.value = true

      if (props.listenerConfig && props.listenerConfig.exclude) {
        if (props.listenerConfig.exclude.includes(evt.type)) {
          return false
        }
      }

      if (evt.type === 'focus') {
        tempVal_.value = RupiahToInt(evt.target.value || 'Rp. 0') || ''
      } else if (evt.type === 'blur') {
        tempVal_.value =
          IntToRupiah(evt.target.value.toString(), 'Rp. ') || 'Rp. 0'
      }

      tempVal.value = RupiahFixValue(evt.target.value)

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
    if (!nativeEdit.value && tempValParent.value[props.rowIndex]) {
      const value = ObjectReader(
        tempValParent.value[props.rowIndex],
        props.field,
      ) as number
      tempVal.value = value
      tempVal_.value = value
        ? IntToRupiah(value.toString(), 'Rp. ')
        : IntToRupiah('0', 'Rp. ')
    }
  })
</script>
