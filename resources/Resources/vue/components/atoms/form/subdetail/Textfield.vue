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
    defineComponent,
    inject,
    onMounted,
    ref,
    watch,
    defineProps,
  } from 'vue'
  import { Ref, UnwrapRef } from 'vue'
  import FormValidation from './Validation.vue'
  import { Element } from '~/types/components/atoms/forms/detail'
  import { ObjectReader, ObjectUpdater, t } from '~/services'
  import { computed } from 'vue'
  import { FormSubDetail } from '~/types/form/subdetail'
  import { TextListenerConfig } from '~/types/components/atoms/forms/detail/text'
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
      type: String,
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
      type: Object as () => TextListenerConfig,
      default: () => {
        return {
          exclude: [],
        }
      },
    },
  })

  const form = inject('form') as FormSubDetail
  const element = inject('element') as Element
  const tempValParent = inject('tempValParent') as Ref<UnwrapRef<Array<any>>>
  const tempVal = ref(
    computed({
      get: () =>
        ObjectReader(tempValParent.value[props.rowIndex], props.field) || '',
      set: (val) =>
        ObjectUpdater(tempValParent.value[props.rowIndex], props.field, val),
    }),
  )
  const tempVal_ = ref('')
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
  const nativeEdit = ref(false)
  // set default
  if (tempVal.value) {
    tempVal_.value = tempVal.value
  } else {
    tempVal_.value = props.default
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

      tempVal.value = tempVal_.value
      if (props.listener) {
        props.listener(element, evt.type, evt.target.value)
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
      tempVal_.value = ObjectReader(
        tempValParent.value[props.rowIndex],
        props.field,
      )
      methods.onChange({
        type: 'change',
        target: { value: tempVal_.value },
      })
    }
  })
</script>
