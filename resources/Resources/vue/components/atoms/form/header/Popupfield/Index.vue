<template>
  <div class="grid grid-cols-12">
    <div :class="{ 'col-span-6': docs, 'col-span-12': !docs }">
      <div :class="{ flex: true, 'flex-column': !inline }">
        <div
          :style="{
            minWidth: inline ? (clientWidth < 640 ? 'unset' : '150px') : '',
            maxWidth: inline ? (clientWidth < 640 ? 'unset' : '150px') : '',
          }"
          :class="{
            'flex mb-2': true,
            'flex-grow-0 mt-3 pl-0': inline && clientWidth > 639,
            'flex-grow pl-0': !inline || clientWidth < 640,
          }"
        >
          <label
            :for="name"
            :class="{
              'block text-xs font-medium text-gray-900 dark:text-gray-300': true,
              'text-red-700 dark:text-red-500':
                properties.errors && properties.errors(name),
            }"
          >
            {{ t(label) }}
            <span
              v-if="properties[name].rules.includes('required')"
              class="text-red-600"
            >
              *
            </span>
          </label>
          <div
            v-if="information"
            class="ft-sz-12 px-2 text-gray-300 dark:text-gray-800"
          >
            <i
              class="fas fa-question-circle cursor-pointer"
              @click="methods.onShowDoc"
            ></i>
          </div>
        </div>
        <div
          :class="{
            'px-0': true,
            'relative flex-grow': inline || clientWidth < 640,
            'flex-grow': !inline || clientWidth < 640,
          }"
        >
          <div class="flex px-0 rounded-lg">
            <input
              :id="name"
              v-model="temp.displayValue"
              autocomplete="off"
              type="text"
              :class="{
                'cursor-pointer bg-gray-50 border border-gray-300 text-gray-900 text-sm focus:ring-blue-500 focus:outline-none focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 disabled:bg-gray-200 disabled:text-gray-400 dark:disabled:text-gray-400 dark:disabled:bg-gray-600': true,
                'bg-red-50 border border-red-500 text-red-900  placeholder-red-700 dark:placeholder-red-700 dark:text-red-700 text-sm focus:ring-red-500 focus:outline-none focus:border-red-500 block w-full p-2.5 dark:bg-red-100 dark:border-red-400':
                  properties.errors && properties.errors(name),
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
              :class="{
                'btn-primary text-white border border-0 px-3.5 flex items-center': true,
                'cursor-pointer': !disabled,
                'cursor-not-allowed': disabled,
              }"
              :style="{
                'border-top-right-radius':
                  !clearable || !temp.displayValue ? 'inherit' : '',
                'border-bottom-right-radius':
                  !clearable || !temp.displayValue ? 'inherit' : '',
              }"
              :disabled="disabled"
              @click="methods.onShowModal"
            >
              <i class="fal fa-search"></i>
            </button>
            <button
              v-if="clearable && temp.displayValue"
              type="button"
              :disabled="disabled"
              :class="{
                'btn-accent text-white border border-0 px-3.5': true,
                'cursor-pointer': !disabled,
                'cursor-not-allowed': disabled,
              }"
              style="
                border-top-right-radius: inherit;
                border-bottom-right-radius: inherit;
              "
              @click="methods.onClear"
            >
              <i class="fal fa-trash-alt"></i>
            </button>
          </div>
          <form-validation
            :name="name"
            :no-margin-top="true"
            :properties="properties"
          />
        </div>
      </div>
    </div>
    <div v-if="docs" :class="{ 'col-span-6 pl-3': true, 'pt-7': !inline }">
      <Highlight language="js" :code="code" />
    </div>
  </div>

  <Modal v-model="temp.showModal" width="w-11/12 max-w-5xl">
    <template #modal-title>
      {{
        isMultiple
          ? t('global.select_multiple_rows')
          : t('global.select_one_row')
      }}
    </template>
    <template #modal-body>
      <Table
        v-if="temp.showModal"
        :is-multiple="isMultiple"
        :api="api"
        :field="field"
        :properties="properties"
        :table-configs="tableConfigs"
      />
    </template>
  </Modal>
</template>

<script setup lang="ts">
  import {
    computed,
    defineComponent,
    defineEmits,
    defineProps,
    inject,
    onMounted,
    provide,
    reactive,
    ref,
    watch,
  } from 'vue'
  import FormValidation from '../Validation.vue'
  import Table from './Table.vue'
  import { Highlight } from '~/components/atoms'
  import { ApiInterface } from '~/types/components/atoms/forms/header'
  import Modal from '~/components/atoms/Modal.vue'
  import {
    Axios,
    Notyf,
    ObjectReader,
    Swal,
    t,
    UpdateWhereParams,
  } from '~/services'
  import { TableConfigs } from '~/types/form/detail'
  import { Field } from '~/types/components/atoms/forms/header/popup'
  import { Lang } from '~/types/form/form-v1'

  defineComponent({
    name: 'Popupfield',
  })

  const props = defineProps({
    name: {
      type: String,
      required: true,
    },
    modelValue: {
      type: [String, Number, Boolean, Array],
      default: null,
    },
    outVal: {
      type: [String, Number, Boolean, Array],
      default: null,
    },
    label: {
      type: String,
      default: 'Label',
    },
    placeholder: {
      type: [String, Object as () => Lang],
      default: '',
    },
    listener: {
      type: Function,
      default: null,
    },
    overrideParams: {
      type: Function as () => void,
      default: null,
    },
    reduce: {
      type: Function,
      default: null,
    },
    clearable: {
      type: Boolean,
      default: false,
    },
    disabled: {
      type: Boolean,
      default: false,
    },
    readonly: {
      type: Boolean,
      default: false,
    },
    hidden: {
      type: Boolean,
      default: false,
    },
    inline: {
      type: Boolean,
      default: false,
    },
    docs: {
      type: Boolean,
      default: false,
    },
    information: {
      type: String,
      default: null,
    },
    properties: {
      type: Object,
      default: null,
    },
    fromGenerator: {
      type: Boolean,
      default: false,
    },
    ready: {
      type: Number,
      default: 0,
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
    field: {
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
  })

  const clientWidth = inject('clientWidth')
  const emit = defineEmits(['update:modelValue', 'update:ready'])

  const temp = reactive({
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
      get: () => props.modelValue,
      set: (val) => emit('update:modelValue', val),
    }),
    ready: computed({
      get: () => props.ready,
      set: (val) => emit('update:ready', val),
    }),
  })

  provide('temp', temp)

  const methods = {
    onChange: (evt) => {
      if (props.listener) {
        props.listener(
          props.properties,
          evt.type,
          props.isMultiple ? evt.value || [] : evt.value || '',
        )
      }
    },
    onShowDoc: () => {
      Notyf({
        type: 'info',
        message: t(props.information),
        duration: 3000,
        ripple: true,
        dismissible: true,
        position: {
          x: 'right',
          y: 'top',
        },
      })
    },
    onShowModal: () => {
      if (props.api) {
        if (!props.api) {
          return
        }
        temp.valueBeforeBlur = temp.value_
        temp.optionBeforeBlur = methods.getValueFull()
        temp.showModal = true
        methods.onChange({ type: 'focus' })
      }
    },
    onCloseModal: () => {
      if (temp.showModal) {
        if (!props.api) {
          return
        } else if (props.api) {
          if (temp.value_ === null && temp.valueBeforeBlur) {
            if (
              temp.options_.findIndex(
                (dt) => dt[props.field.value] === temp.valueBeforeBlur,
              ) < 0
            ) {
              temp.options_.push(temp.optionBeforeBlur)
            }
            temp.value_ = temp.valueBeforeBlur
          }
          temp.valueBeforeBlur = null
        }
        temp.showModal = false
        methods.onChange({ type: 'blur' })
      }
    },
    onClear: () => {
      temp.displayValue = ''
      temp.value = props.isMultiple ? [] : ''
      methods.onChange({ type: 'clear' })
    },
    // value is used when updated from outside component
    onSetDisplayValue: (value = null) => {
      return new Promise((resolve) => {
        let url = ''
        if (props.api.url !== undefined) {
          url = props.api.url
        } else if (props.api.model !== undefined) {
          url = temp.apiOperationUrl + `/${props.api.model}`
        }

        const params: any = {}
        if (props.api.parameters) {
          Object.entries(props.api.parameters).forEach(([key, val]) => {
            params[key] = val
          })
        }
        if (props.isMultiple) {
          // still not support multiple select with encrypted id
          params.wherein = `${props.field.value}=${
            value ? JSON.stringify(value) : JSON.stringify(props.default)
          }`
        } else {
          let fixValue = value ? value : props.default

          params.where = UpdateWhereParams(params.where, props.field, fixValue)
        }
        url = url + '?' + new URLSearchParams(params)

        Axios({
          url,
          method: 'get',
        })
          .then((res: any) => {
            resolve(true)
            if (props.isMultiple) {
              const tempValue = []
              res.data.data.forEach((dt, di) => {
                tempValue.push(ObjectReader(dt, props.field.value))
                if (!di) {
                  temp.displayValue += ObjectReader(dt, props.field.display)
                } else {
                  temp.displayValue +=
                    ', ' + ObjectReader(dt, props.field.display)
                }
              })
              temp.value = tempValue
              temp.value_ = tempValue
            } else {
              temp.value = ObjectReader(res.data.data[0], props.field.value)
              temp.value_ = ObjectReader(res.data.data[0], props.field.value)
              temp.displayValue = ObjectReader(
                res.data.data[0],
                props.field.display,
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
          options = temp.options_.filter((dt) => {
            // eslint-disable-next-line
          return temp.value_.includes(dt[props.field.value].toString())
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
          option = temp.options_.find((dt) => {
            // eslint-disable-next-line
          return dt[props.field.value] == temp.value_
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
  provide('compParentMethods', methods)
  onMounted(() => {
    if (props.default) {
      methods.onSetDisplayValue().then(() => {
        temp.ready++
      })
    } else {
      setTimeout(() => {
        temp.ready++
      }, 200)
    }
  })

  const nativeEdit = ref(false)
  provide('nativeEdit', nativeEdit)

  watch(
    () => props.outVal,
    () => {
      if (!nativeEdit.value && temp.value !== props.outVal) {
        console.log(props.outVal)
        methods.onSetDisplayValue(props.outVal)
      }
    },
  )

  const code = ref(`
  {
    type: 'popupfield',
    value: '',
    items: [],
    col: 'col-span-12 lg:col-span-6',
    options: {
      label: 'Popup',
      information: 'Help text example: Popup',
      inline: true,
      placeholder: 'Select one',
      readonly: false,
      disabled: false,
      hidden: false,
      multiple: false,
      clearable: true,
      field: {
        value: 'objectId',
        display: 'name',
        columns: [
          {
            label: 'Name',
            field: 'name',
            filter: true,
            filterOptions: {
              enabled: true // enable filter for this column
            }
          },
          {
            label: 'Slug',
            field: 'slug',
            filter: true,
            filterOptions: {
              enabled: true // enable filter for this column
            }
          },
          {
            type: 'image',
            style: { width: '30px', height: '30px' },
            label: 'Icon',
            field: 'icon.url',
            filter: false,
            filterOptions: {
              enabled: false
            }
          }
        ]
      },
      api: {
        gql: gql\`
          query TopicCategory {
            topicCategories {
              results {
                objectId
                name
                slug
                icon {
                  url
                }
              }
            }
          }
        \`,
        output: 'topicCategories.results'
      }
    },
    listener: (element, type, value) => {
      // console.log(type, value)
    },
    rules: ['required']
  }
`)
</script>
