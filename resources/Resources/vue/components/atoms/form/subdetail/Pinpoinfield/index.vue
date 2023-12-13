<template>
  <div>
    <div class="flex items-center select2-detail">
      <div class="flex px-0 rounded-lg">
        <input
          :id="elementId"
          v-model="tempVal.displayValue"
          autocomplete="off"
          type="text"
          :class="{
            'cursor-not-allowed bg-gray-50 border border-gray-300 text-gray-900 text-sm focus:ring-blue-500 focus:outline-none focus:border-blue-500 block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 disabled:bg-gray-200 disabled:text-gray-400 dark:disabled:text-gray-400 dark:disabled:bg-gray-600': true,
            'bg-red-50 border border-red-500 text-red-900 placeholder-red-700 dark:text-red-700 dark:placeholder-red-700 text-sm focus:ring-red-500 focus:outline-none focus:border-red-500 block w-full p-1.5 dark:bg-red-100 dark:border-red-400':
              tempValParent[rowIndex].errors &&
              tempValParent[rowIndex].errors(
                tempValParent,
                form,
                rowIndex,
                field,
              ),
          }"
          style="
            border-top-left-radius: inherit;
            border-bottom-left-radius: inherit;
          "
          :placeholder="t(placeholder)"
          :disabled="disabled || false"
          :readonly="true"
        />
        <button
          class="btn-primary border border-0 cursor-pointer flex justify-center items-center px-0"
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
          <i class="fal fa-search"></i>
        </button>
        <button
          v-if="clearable && tempVal.displayValue"
          type="button"
          class="btn-accent border border-0 cursor-pointer flex justify-center items-center px-0"
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
      :rows="tempValParent"
      :row-index="rowIndex"
      :field="field"
      :parent-editor="parentEditor"
    />
  </div>

  <Modal v-model="tempVal.showModal" width="w-11/12 max-w-5xl">
    <template #modal-title>
      {{
        isMultiple
          ? $t('global.select_multiple_rows')
          : $t('global.select_one_row')
      }}
    </template>
    <template #modal-body>
      <Map
        v-if="tempVal.showModal"
        :is-multiple="isMultiple"
        :api="api"
        :field="selectField"
        :properties="element"
        :table-configs="tableConfigs"
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
    Ref,
    UnwrapRef,
  } from 'vue'
  import FormValidation from '../Validation.vue'
  import Map from './Map.vue'
  import { Element } from '~/types/components/atoms/forms/detail'
  import { ObjectReader, ObjectUpdater, t } from '~/services'
  import Modal from '~/components/atoms/Modal.vue'
  import { FormSubDetail } from '~/types/form/subdetail'
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
      type: [String, Array],
      default: '',
    },
    clearable: {
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

  const isReady = ref(false)

  const tempVal = reactive({
    value_: null,
    displayValue: '',
    showModal: false,
    value: computed({
      get: () =>
        ObjectReader(tempValParent.value[props.rowIndex], props.field) || '',
      set: (val) =>
        ObjectUpdater(tempValParent.value[props.rowIndex], props.field, val),
    }),
  })
  const tempValParent = inject('tempValParent') as Ref<UnwrapRef<Array<any>>>

  provide('tempVal', tempVal)

  const elementId = ref(
    `${props.uniqueId}-${props.field}-${props.rowIndex}`.replace('.', '-'),
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
        tempVal.value = []
        tempVal.value = []
      } else {
        tempVal.value = ''
        tempVal.value = ''
      }
      methods.onChange({ type: 'clear' })
    },
    onSetDisplayValue: () => {
      //
    },
  }

  onMounted(() => {
    form.ready++
    isReady.value = true
  })

  const nativeEdit = ref(false)
  provide('nativeEdit', nativeEdit)
</script>
