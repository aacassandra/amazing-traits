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
  import slugify from 'slugify'
  import FormValidation from './Validation.vue'
  import { Highlight } from '~/components/atoms'
  import { Notyf, t } from '~/services'

  const onSlugify = (value: string): string => {
    return slugify(value, {
      remove: /[*+~.()'"!:@]/g, // remove characters that match regex, defaults to `undefined`
      replacement: '-', // replace spaces with replacement character, defaults to `-`
      lower: true, // convert to lower case, defaults to `false`
      strict: true, // strip special characters except replacement, defaults to `false`
      locale: 'id', // language code of the locale to use
      trim: true, // trim leading and trailing replacement chars, defaults to `true`
    })
  }
  import { Lang } from '~/types/form/form-v1'

  defineComponent({
    name: 'FormSlug',
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

  const onBlur = ref(false)

  const nativeEdit = ref(false)
  const methods = {
    onChange: (evt) => {
      if (evt.type === 'blur') {
        onBlur.value = true
        temp.value_ = onSlugify(evt.target.value)
      } else if (evt.type === 'focus') {
        onBlur.value = false
        temp.value_ = evt.target.value.replace(/-/g, ' ')
      }

      temp.value = temp.value_
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
      temp.value_ = onSlugify(temp.value)
    } else {
      temp.value_ = onSlugify(props.default)
    }
  } else {
    if (temp.value) {
      temp.value_ = onSlugify(temp.value)
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
        temp.value = onSlugify(props.outVal)
        if (onBlur.value) {
          temp.value_ = onSlugify(props.outVal)
        } else {
          temp.value_ = props.outVal.replace(/-/g, '')
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
