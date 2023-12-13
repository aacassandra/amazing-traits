<template>
  <div>
    <div class="flex items-center select2-detail">
      <div class="flex px-0 rounded-lg w-full">
        <input
          :id="elementId"
          v-model="tempVal.displayValue"
          autocomplete="off"
          type="text"
          :class="{
            'cursor-pointer bg-gray-50 border border-gray-300 text-gray-900 text-sm focus:ring-blue-500 focus:outline-none focus:border-blue-500 block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 disabled:bg-gray-200 disabled:text-gray-400 dark:disabled:text-gray-400 dark:disabled:bg-gray-600': true,
            'bg-red-50 border border-red-500 text-red-900 placeholder-red-700 dark:text-red-700 dark:placeholder-red-700 text-sm focus:ring-red-500 focus:outline-none focus:border-red-500 block w-full p-1.5 dark:bg-red-100 dark:border-red-400':
              temp.form.rows[rowIndex].errors &&
              temp.form.rows[rowIndex].errors(temp.form, rowIndex, field),
          }"
          style="
            border-top-left-radius: inherit;
            border-bottom-left-radius: inherit;
          "
          :placeholder="t(placeholder)"
          :disabled="disabled || false"
          :readonly="true"
          @click="methods.onShowModal"
        />
        <button
          class="btn-primary focus:outline-none focus:ring-0 text-white cursor-pointer flex justify-center items-center px-0"
          :style="{
            minWidth: '32px',
            maxWidth: '32px',
            'border-top-right-radius':
              !clearable || !tempVal.displayValue ? 'inherit' : '',
            'border-bottom-right-radius':
              !clearable || !tempVal.displayValue ? 'inherit' : '',
          }"
          @click="methods.onShowModal"
        >
          <i class="fal fa-map-pin"></i>
        </button>
        <button
          v-if="clearable && tempVal.displayValue"
          type="button"
          class="btn-accent text-white cursor-pointer flex justify-center items-center px-0"
          style="
            min-width: 32px;
            max-width: 32px;
            border-top-right-radius: inherit;
            border-bottom-right-radius: inherit;
          "
          @click="methods.onClear"
        >
          <i class="fal fa-trash-alt"></i>
        </button>
      </div>
    </div>
    <form-validation
      :rows="temp.form.rows"
      :row-index="rowIndex"
      :field="field"
      :form="temp.form"
    />
  </div>

  <Modal v-model="tempVal.showModal" width="w-11/12 max-w-5xl">
    <template #modal-title>
      {{ isMultiple ? 'Select Multiple Location' : 'Select One Location' }}
    </template>
    <template #modal-body>
      <Map
        v-if="tempVal.showModal"
        :is-multiple="isMultiple"
        :properties="element"
      />
    </template>
  </Modal>
</template>

<script setup lang="ts">
  import {
    computed,
    defineComponent,
    defineProps,
    inject,
    onMounted,
    provide,
    reactive,
    ref,
    watch,
  } from 'vue'
  import FormValidation from '../Validation.vue'
  import Map from './Map.vue'
  import { FormDetail } from '~/types/form/detail'
  import { Element } from '~/types/components/atoms/forms/detail'
  import { GetRandomString, ObjectReader, ObjectUpdater, t } from "~/services";
  import Modal from '~/components/atoms/Modal.vue'
  import { Lang } from '~/types/form/form-v1'

  defineComponent({
    name: 'PopupfieldDetail',
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
    isMultiple: {
      type: Boolean,
      default: false,
    },
    default: {
      type: [String, Number, Boolean, Array],
      default: '',
    },
    clearable: {
      type: Boolean,
      default: false,
    },
  })

  const temp = inject('temp') as {
    form: FormDetail
  }
  const element = inject('element') as Element

  const isReady = ref(false)

  const tempVal = reactive({
    value_: null,
    displayValue: '',
    showModal: false,
    value: computed({
      get: () => ObjectReader(temp.form.rows[props.rowIndex], props.field),
      set: (val) =>
        ObjectUpdater(temp.form.rows[props.rowIndex], props.field, val),
    }),
  })

  if (props.isMultiple && !tempVal.value) {
    tempVal.value = []
    tempVal.value_ = []
  }

  provide('tempVal', tempVal)

  const uid = GetRandomString(5)
  const elementId = ref(
    `${uid}-${props.field}-${props.rowIndex}`.replace('.', '-'),
  )

  const methods = {
    onChange: (evt) => {
      if (props.listener) {
        props.listener(element, evt.type, tempVal.value)
      }
    },
    onShowModal: () => {
      tempVal.showModal = true
      methods.onChange({ type: 'focus' })
    },
    onCloseModal: () => {
      if (tempVal.showModal) {
        tempVal.showModal = false
        methods.onChange({ type: 'blur' })
      }
    },
    onClear: () => {
      tempVal.displayValue = ''
      if (props.isMultiple) {
        tempVal.value_.length = 0
        tempVal.value.length = 0
      } else {
        tempVal.value_ = null
        tempVal.value = null
      }
      // methods.onChange({ type: 'clear' })
    },
    onSetDisplayValue: () => {
      //
    },
  }

  onMounted(() => {
    temp.form.ready++
    isReady.value = true
  })

  const nativeEdit = ref(false)
  provide('nativeEdit', nativeEdit)

  watch(temp, () => {
    if (temp.form.rows[props.rowIndex] && !nativeEdit.value) {
      tempVal.value_ = ObjectReader(temp.form.rows[props.rowIndex], props.field)
      if (tempVal.value_ !== undefined) {
        if (props.isMultiple) {
          tempVal.displayValue = tempVal.value_.length
            ? `${tempVal.value_.length} Locations`
            : ''
        } else {
          tempVal.displayValue = tempVal.value_ ? `1 Location` : ''
        }
      } else {
        tempVal.displayValue = ''
      }
    }
  })
</script>
