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
            v-model="temp.value_"
            autocomplete="off"
            type="text"
            :class="{
              'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:outline-none focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 disabled:bg-gray-200 disabled:text-gray-400 dark:disabled:text-gray-400 dark:disabled:bg-gray-600': true,
              'bg-red-50 border border-red-500 text-red-900  placeholder-red-700 dark:placeholder-red-700 dark:text-red-700 text-sm rounded-lg focus:ring-red-500 focus:outline-none focus:border-red-500 block w-full p-2.5 dark:bg-red-100 dark:border-red-400':
                properties.errors && properties.errors(name),
            }"
            :placeholder="t(placeholder)"
            :disabled="disabled"
            :readonly="readonly"
            @change="methods.onChange"
            @blur="methods.onChange"
            @focus="methods.onChange"
            @keyup="methods.onChange"
            @keydown="methods.onChange"
          />
          <div>
            <form-validation
              :name="name"
              :no-margin-top="true"
              :properties="properties"
            />
          </div>
        </div>
      </div>
    </div>
    <div v-if="docs" :class="{ 'col-span-6 pl-3': true, 'pt-7': !inline }">
      <Highlight language="js" :code="code" />
    </div>
  </div>
</template>

<script setup lang="ts">
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
  import FormValidation from './Validation.vue'
  import { Highlight } from '~/components/atoms'
  import { PhoneFormatter, Notyf, t } from '~/services'
  import { Lang } from '~/types/form/form-v1'

  defineComponent({
    name: 'FormPhone',
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
    ready: {
      type: Number,
      default: 0,
    },
    default: {
      type: String,
      default: '',
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

  const onBlur = ref(true)

  const nativeEdit = ref(false)
  const methods = {
    onChange: (evt) => {
      if (evt.type === 'blur') {
        onBlur.value = true
        temp.value_ = PhoneFormatter(
          evt.target.value,
          props.display,
          props.countryCode,
        )
      } else if (evt.type === 'focus') {
        onBlur.value = false
        temp.value_ = evt.target.value.replace(/([^\w]+|\s+)/g, '')
        if (props.countryCode) {
          temp.value_ = temp.value_.replace(props.countryCode, 0)
        }
      }

      temp.value = PhoneFormatter(
        evt.target.value,
        props.output,
        props.countryCode,
        false,
      )
      nativeEdit.value = true

      if (props.listener) {
        props.listener(props.properties, evt.type, evt.target.value)
      }

      setTimeout(() => {
        nativeEdit.value = false
      }, 100)
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

  if (props.default) {
    // set default value
    if (temp.value) {
      temp.value_ = PhoneFormatter(temp.value, props.display, props.countryCode)
    } else {
      temp.value_ = PhoneFormatter(
        props.default,
        props.display,
        props.countryCode,
      )
    }
  } else {
    if (temp.value) {
      temp.value_ = PhoneFormatter(temp.value, props.display, props.countryCode)
    } else {
      temp.value_ = ''
    }
  }

  onMounted(() => {
    if (props.fromGenerator) {
      setTimeout(() => {
        temp.ready++
      }, 100)
    }
  })

  watch(
    () => props.outVal,
    () => {
      if (!nativeEdit.value && temp.value !== props.outVal) {
        temp.value = props.outVal

        if (onBlur.value) {
          temp.value_ = PhoneFormatter(
            props.outVal,
            props.display,
            props.countryCode,
          )
        } else {
          temp.value_ = props.outVal.replace(/([^\w]+|\s+)/g, '')
        }
      }
    },
  )

  const code = ref(`
  {
    type: 'slugfield',
    value: '',
    col: 'col-span-12 col-span-lg-6',
    options: {
      label: 'URL Slug',
      information: 'Help text example: URL Slug',
      inline: true,
      readonly: false,
      hidden: false
    },
    listener: (element, type, value) => {
      //
    },
    rules: ['required']
  }
`)
</script>
