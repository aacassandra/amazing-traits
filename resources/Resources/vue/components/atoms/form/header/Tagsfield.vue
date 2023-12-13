<template>
  <div class="grid grid-cols-12">
    <div :class="{ 'col-span-6': docs, 'col-span-12': !docs }">
      <div
        :class="{
          flex: true,
          'flex-column': !inline,
          'is-invalid': properties.errors && properties.errors(name),
        }"
      >
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
            ref="inputRef"
            autocomplete="off"
            :class="{
              hidden: isReady,
              'focus:outline-none': true,
              'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 focus:outline-none block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500': true,
              'text-sm rounded-lg focus:outline-none block w-full p-2.5': true,
              'is-invalid': properties.errors && properties.errors(name),
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
  import Tagify from '@yaireo/tagify'
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
  import '~/assets/css/custom.tagify.css'
  import { Highlight } from '~/components/atoms'
  import { Notyf, t } from '~/services'
  import { Lang } from '~/types/form/form-v1'

  defineComponent({
    name: 'Tagsfield',
  })

  const props = defineProps({
    name: {
      type: String,
      required: true,
    },
    modelValue: {
      type: Array,
      default: null,
    },
    outVal: {
      type: Array,
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
      type: Array,
      default: null,
    },
  })

  const clientWidth = inject('clientWidth')
  const emit = defineEmits(['update:modelValue', 'update:ready'])
  const temp = reactive({
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

  const inputRef = ref()

  const isReady = ref(false)
  const tagify = ref()

  const outValCurrent = ref('')

  const methods = {
    onChange: (evt) => {
      outValCurrent.value = JSON.stringify(props.properties[props.name].value)

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
    // eslint-disable-next-line no-new
    tagify.value = new Tagify(inputRef.value)
    isReady.value = true

    tagify.value
      .on('add', () => {
        methods.onChange({ type: 'add' })
      })
      .on('remove', () => {
        methods.onChange({ type: 'remove' })
      })
      .on('input', () => {
        methods.onChange({ type: 'input' })
      })
      .on('edit', () => {
        methods.onChange({ type: 'edit' })
      })
      .on('invalid', () => {
        methods.onChange({ type: 'invalid' })
      })
      .on('click', () => {
        methods.onChange({ type: 'click' })
      })
      .on('focus', () => {
        methods.onChange({ type: 'focus' })
      })
      .on('blur', () => {
        methods.onChange({ type: 'blur' })
      })
      .on('change', (e) => {
        nativeEdit.value = true
        if (e.detail.value.length) {
          temp.value.length = 0
          JSON.parse(e.detail.value).forEach((x: any) => {
            temp.value.push(x.value)
          })
        } else {
          temp.value = []
        }
        methods.onChange({ type: 'change' })

        setTimeout(() => {
          nativeEdit.value = false
        }, 100)
      })

    // set default value
    props.default.forEach((dt) => {
      tagify.value.addTags(dt)
    })

    if (props.fromGenerator) {
      setTimeout(() => {
        temp.ready++
      }, 100)
    }
  })

  const isUpdate = ref(false)

  watch(
    props.properties,
    () => {
      if (
        isReady.value &&
        !nativeEdit.value &&
        props.outVal &&
        !isUpdate.value
      ) {
        isUpdate.value = true

        if (props.outVal.length) {
          if (outValCurrent.value !== JSON.stringify(props.outVal)) {
            tagify.value.removeAllTags()
            props.outVal.forEach((dt) => {
              tagify.value.addTags(dt)
            })
            temp.value = props.outVal
            methods.onChange({ type: 'change' })
          }
        } else {
          tagify.value.removeAllTags()
          temp.value = []
          methods.onChange({ type: 'change' })
        }

        setTimeout(() => {
          isUpdate.value = false
        }, 300)
      }
    },
    {
      deep: true,
    },
  )

  const code = ref(`
  {
    type: 'tagsfield',
    value: '',
    col: 'col-12 col-lg-6',
    options: {
      label: 'Tags',
      information: 'Help text example: Tags',
      inline: true,
      placeholder: 'tags1, tags2, ...',
      readonly: false,
      disabled: false,
      hidden: false
    },
    listener: (element, type, value) => {
      // console.log(type)
    },
    rules: ['required']
  }
`)
</script>
