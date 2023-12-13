<template>
  <div class="grid grid-cols-12">
    <div :class="{ 'col-span-6': docs, 'col-span-12': !docs }">
      <div :class="{ flex: true, 'flex-column': !inline }">
        <div
          v-if="label"
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
            :name="name"
            autocomplete="off"
            style="max-height: 42px"
            type="text"
            :class="{
              'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:outline-none focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 disabled:bg-gray-200 disabled:text-gray-400 dark:disabled:text-gray-400 dark:disabled:bg-gray-600': true,
              'bg-red-50 border border-red-500 text-red-900  placeholder-red-700 dark:placeholder-red-700 dark:text-red-700 text-sm rounded-lg focus:ring-red-500 focus:outline-none focus:border-red-500 block w-full p-2.5 dark:bg-red-100 dark:border-red-400':
                fromGenerator && properties.errors && properties.errors(name),
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
          <form-validation
            v-if="fromGenerator"
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
  import { Notyf, t } from '~/services'
  import { Lang } from '~/types/form/form-v1'

  defineComponent({
    name: 'Textfield',
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
      default: '',
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
  })

  const clientWidth = inject('clientWidth')
  const emit = defineEmits([
    'update:modelValue',
    'update:ready',
    'update:outVal',
  ])
  const temp = reactive({
    value_: '',
    value: computed({
      get: () => props.modelValue,
      set: (val) => emit('update:modelValue', val),
    }),
    outVal: computed({
      get: () => props.outVal,
      set: (val) => emit('update:outVal', val),
    }),
    ready: computed({
      get: () => props.ready,
      set: (val) => emit('update:ready', val),
    }),
  })

  // set default value
  if (temp.value) {
    temp.value_ = temp.value
  } else {
    temp.value_ = props.default
  }

  const nativeEdit = ref(false)

  const methods = {
    onChange: (evt) => {
      if (evt.type !== 'mounted') {
        nativeEdit.value = true
        // if (['change', 'keyup', 'keydown'].includes(evt.type)) {
        //   temp.outVal = temp.value
        // }
      }

      temp.value = evt.target.value
      if (props.listener) {
        props.listener(props.properties, evt.type, temp.value)
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

  onMounted(() => {
    if (props.fromGenerator) {
      setTimeout(() => {
        methods.onChange({ type: 'mounted', target: { value: temp.value } })
        temp.ready++
      }, 100)
    }
  })

  watch(
    () => props.outVal,
    () => {
      if (!nativeEdit.value && temp.value !== props.outVal) {
        temp.value_ = props.outVal
        methods.onChange({
          type: 'change',
          target: {
            value: temp.value_,
          },
        })
      }
    },
  )

  const code = ref(`
  {
    type: 'textfield',
    value: '',
    col: 'col-span-12 col-span-lg-6',
    options: {
      label: 'Text',
      information: 'Help text example: Text',
      inline: true,
      placeholder: 'Write a text here',
      readonly: false,
      disabled: false,
      hidden: false
    },
    listener: (element, type, value) => {
      //
    },
    rules: ['required', 'min:5', 'max:10']
  }
`)
</script>
