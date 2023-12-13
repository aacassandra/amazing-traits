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
          :disabled="disabled"
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
          :disabled="disabled"
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
      <Table
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
  import Table from './Table.vue'
  import { ApiInterface, Element } from '~/types/components/atoms/forms/detail'
  import {
    ObjectReader,
    ObjectUpdater,
    Swal,
    UpdateWhereParams,
    t,
  } from '~/services'
  import Modal from '~/components/atoms/Modal.vue'
  import { FormSubDetail } from '~/types/form/subdetail'
  import { TableConfigs } from '~/types/form/detail'
  import axios from 'axios'
  import { Field } from '~/types/components/atoms/forms/header/popup'
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
    overrideParams: {
      type: Function as () => void,
      default: null,
    },
    reduce: {
      type: Function,
      default: null,
    },
    selectField: {
      type: Object as () => Field,
      default: null,
    },
    isMultiple: {
      type: Boolean,
      default: false,
    },
    api: {
      type: Object as () => ApiInterface,
      default: (): ApiInterface => {
        return null
      },
    },
    default: {
      type: [String, Number, Boolean, Array],
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
    tableConfigs: {
      type: Object as () => TableConfigs,
      default: () => {
        return {
          fixedHeader: true,
          maxHeight: '350px',
          lineNumbers: false,
        }
      },
    },
  })

  const form = inject('form') as FormSubDetail
  const element = inject('element') as Element

  const isReady = ref(false)

  const tempVal = reactive({
    value_: null,
    valueBeforeBlur: null,
    options_: [],
    optionBeforeBlur: null, // is object
    fetchController: null,
    apiOperationUrl: import.meta.env.VITE_APP_API_CRUD,
    search: '',
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
      if (props.api) {
        if (!props.api) {
          return
        }
        tempVal.valueBeforeBlur = tempVal.value_
        tempVal.optionBeforeBlur = methods.getValueFull()
        tempVal.showModal = true
        methods.onChange({ type: 'focus' })
      }
    },
    onCloseModal: () => {
      if (tempVal.showModal) {
        if (!props.api) {
          return
        } else if (props.api) {
          if (tempVal.value_ === null && tempVal.valueBeforeBlur) {
            if (
              tempVal.options_.findIndex(
                (dt) => dt[props.selectField.value] === tempVal.valueBeforeBlur,
              ) < 0
            ) {
              tempVal.options_.push(tempVal.optionBeforeBlur)
            }
            tempVal.value_ = tempVal.valueBeforeBlur
          }
          tempVal.valueBeforeBlur = null
        }
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
    onSetDisplayValue: (value = null) => {
      return new Promise((resolve) => {
        let url = ''
        if (props.api.url !== undefined) {
          url = props.api.url
        } else if (props.api.model !== undefined) {
          url = tempVal.apiOperationUrl + `/${props.api.model}`
        }

        const params: any = {}
        if (props.api.parameters) {
          Object.entries(props.api.parameters).forEach(([key, val]) => {
            params[key] = val
          })
        }
        if (props.isMultiple) {
          params.wherein = `${props.selectField.value}=${JSON.stringify(
            props.default,
          )}`
        } else {
          let fixValue = value ? value : props.default
          params.where = UpdateWhereParams(
            params.where,
            props.selectField,
            fixValue,
          )
        }

        url = url + '?' + new URLSearchParams(params)

        axios({
          url,
          method: 'get',
        })
          .then((res) => {
            resolve(true)
            if (props.isMultiple) {
              const tempValue = []
              res.data.data.forEach((dt) => {
                tempValue.push(ObjectReader(dt, props.selectField.value))
              })
              tempVal.displayValue = res.data.data.length + ' items selected'
              tempVal.value = tempValue
              tempVal.value_ = tempValue
            } else {
              tempVal.value = ObjectReader(
                res.data.data[0],
                props.selectField.value,
              )
              tempVal.value_ = ObjectReader(
                res.data.data[0],
                props.selectField.value,
              )
              tempVal.displayValue = ObjectReader(
                res.data.data[0],
                props.selectField.display,
              )
            }
          })
          .catch((err) => {
            Swal.basic({
              icon: 'error',
              title: `Error ${err.response.data.statusCode}!`,
              html: err.response.data.statusMessage,
              button: {
                confirm: 'default',
                size: 'md',
              },
            })
          })
      })
    },
    getValueFull: () => {
      if (props.isMultiple) {
        let options = []
        try {
          options = tempVal.options_.filter((dt) => {
            // eslint-disable-next-line
          return tempVal.value_.includes(dt[props.selectField.value].toString())
          })
        } catch (e) {
          options = []
        }

        if (typeof props.reduce === 'function') {
          return props.reduce(options ?? [])
        } else {
          return options ?? []
        }
      } else {
        let option = {}
        try {
          option = tempVal.options_.find((dt) => {
            // eslint-disable-next-line
          return dt[props.selectField.value] == tempVal.value_
          })
        } catch (e) {
          option = {}
        }

        if (typeof props.reduce === 'function') {
          return props.reduce(option ?? {})
        } else {
          return option ?? {}
        }
      }
    },
  }

  onMounted(() => {
    if (props.default) {
      if (props.isMultiple) {
        if (tempVal.value && tempVal.value.length) {
          methods.onSetDisplayValue(tempVal.value).then(() => {
            form.ready++
            isReady.value = true
          })
        }
      } else if (tempVal.value) {
        methods.onSetDisplayValue(tempVal.value).then(() => {
          form.ready++
          isReady.value = true
        })
      } else {
        methods.onSetDisplayValue(props.default).then(() => {
          form.ready++
          isReady.value = true
        })
      }
    } else {
      form.ready++
      isReady.value = true
    }
  })

  const nativeEdit = ref(false)
  provide('nativeEdit', nativeEdit)
</script>
