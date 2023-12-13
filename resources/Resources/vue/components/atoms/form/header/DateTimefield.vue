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
          <input
            :id="name"
            autocomplete="off"
            :class="{
              [`flatpickr-${name}-datetime`]: true,
              'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:outline-none focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 disabled:bg-gray-200 disabled:text-gray-400 dark:disabled:text-gray-400 dark:disabled:bg-gray-600': true,
              'bg-red-50 border border-red-500 text-red-900  placeholder-red-700 dark:placeholder-red-700 dark:text-red-700 text-sm rounded-lg focus:ring-red-500 focus:outline-none focus:border-red-500 block w-full p-2.5 dark:bg-red-100 dark:border-red-400':
                properties.errors && properties.errors(name),
            }"
            type="text"
            :placeholder="t(placeholder)"
            :disabled="disabled"
            :readonly="readonly"
          />
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
</template>

<script setup lang="ts">
  import FormValidation from './Validation.vue'
  import {
    computed,
    defineComponent,
    inject,
    onMounted,
    reactive,
    ref,
    watch,
    defineProps,
    defineEmits,
  } from 'vue'
  import { Highlight } from '~/components/atoms'
  import moment from 'moment'
  import { Notyf, Flatpickr, t } from '~/services'
  import { Lang } from '~/types/form/form-v1'

  defineComponent({
    name: 'DateTimefield',
  })

  const props = defineProps({
    name: {
      type: String,
      required: true,
    },
    modelValue: {
      type: String,
      default: null,
    },
    outVal: {
      type: String,
      default: null,
    },
    input: {
      type: String,
      default: 'YYYY-MM-DD HH:mm',
    },
    output: {
      type: String,
      default: 'YYYY-MM-DD HH:mm',
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
    default: {
      type: String as () => 'inherit' | 'now' | '',
      default: '',
    },
    ready: {
      type: Number,
      default: 0,
    },
    in24h: {
      type: Boolean,
      default: false,
    },
  })

  const clientWidth = inject('clientWidth')
  const emit = defineEmits(['update:modelValue', 'update:ready'])
  const temp = reactive({
    value_: null,
    value: computed({
      get: () => props.modelValue,
      set: (val) => emit('update:modelValue', val),
    }),
    ready: computed({
      get: () => props.ready,
      set: (val) => emit('update:ready', val),
    }),
  })

  const nativeEdit = ref(false)

  const isReady = ref(false)
  const init = ref()

  const methods = {
    onChange: (evt) => {
      if (props.listener) {
        props.listener(props.properties, evt.type, temp.value)
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
  }

  onMounted(() => {
    temp.value_ =
      props.default === 'inherit'
        ? moment(temp.value, props.input).format(props.output)
        : props.default === 'now'
        ? moment(new Date(), props.input).format(props.output)
        : props.default
    setTimeout(() => {
      init.value = Flatpickr(`.flatpickr-${props.name}-datetime`, {
        enableTime: true,
        time_24hr: props.in24h,
        // https://flatpickr.js.org/formatting/
        dateFormat: 'd-m-Y H:i',
        defaultDate: temp.value_,
        onDestroy() {
          init.value.destroy()
          methods.onChange({ type: 'destroy' })
        },
        onReady() {
          isReady.value = true
          if (props.fromGenerator) {
            setTimeout(() => {
              temp.ready++
            }, 100)
          }
          methods.onChange({ type: 'ready' })
        },
        onChange(_, dateStr) {
          nativeEdit.value = true
          temp.value = moment(dateStr, props.output).format(props.input)
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

  watch(
    () => props.outVal,
    () => {
      if (isReady.value && !nativeEdit.value && temp.value !== props.outVal) {
        temp.value_ = moment(props.outVal, props.input).format(props.output)
        temp.value = props.outVal
        setTimeout(() => {
          init.value.setDate(temp.value_)
        }, 100)
      }
    },
  )

  const code = ref(`
    {
      type: 'datetimefield',
      value: '',
      col: 'col-span-12 col-span-lg-6',
      options: {
        label: 'DateTime',
        information: 'Help text example: DateTime',
        inline: true,
        placeholder: 'YYYY-MM-DD HH:mm',
        format: 'Y-m-d H:i',
        readonly: false,
        disabled: false,
        hidden: false
      },
      listener: (element, type, value) => {
        //
      },
      rules: ['required']
    }
  `)
</script>
