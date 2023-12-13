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
                'cursor-not-allowed bg-gray-50 border border-gray-300 text-gray-900 text-sm focus:ring-blue-500 focus:outline-none focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 disabled:bg-gray-200 disabled:text-gray-400 dark:disabled:text-gray-400 dark:disabled:bg-gray-600': true,
                'bg-red-50 border border-red-500 text-red-900  placeholder-red-700 dark:placeholder-red-700 dark:text-red-700 text-sm focus:ring-red-500 focus:outline-none focus:border-red-500 block w-full p-2.5 dark:bg-red-100 dark:border-red-400':
                  properties.errors && properties.errors(name),
              }"
              style="
                border-top-left-radius: inherit;
                border-bottom-left-radius: inherit;
              "
              :placeholder="t(placeholder)"
              :disabled="disabled"
              :readonly="true"
            />
            <input
              ref="picker"
              type="file"
              class="hidden"
              @change="methods.onChange"
            />
            <button
              v-if="
                temp.linkPreview && (viewer === 'image' || viewer === 'pdf')
              "
              :class="{
                'btn-secondary border border-0 flex items-center': true,
                'cursor-pointer': true,
              }"
              style="padding: 0 17px"
              :style="{
                padding: '0 17px',
                'border-top-right-radius':
                  !clearable || !temp.displayValue || disabled ? 'inherit' : '',
                'border-bottom-right-radius':
                  !clearable || !temp.displayValue || disabled ? 'inherit' : '',
              }"
              @click="methods.onShowModal"
            >
              <i :class="`fal fa-file-${viewer}`"></i>
            </button>
            <button
              v-else-if="!disabled"
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
              @click="methods.onPickFile"
            >
              <i class="fal fa-folder-open"></i>
            </button>
            <button
              v-if="clearable && temp.displayValue && !disabled"
              type="button"
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
              <i class="fas fa-trash-alt"></i>
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

  <Modal v-model="temp.showModal" width="max-w-5xl">
    <template #modal-title>
      {{
        viewer === 'image'
          ? 'Image Viewer'
          : viewer === 'pdf'
          ? 'PDF Viewer'
          : ''
      }}
    </template>
    <template #modal-body>
      <div class="w-full flex justify-center">
        <img
          v-if="viewer === 'image'"
          :src="temp.linkPreview"
          alt=""
          style="max-width: 650px; object-fit: scale-down"
        />
        <embed
          v-else-if="viewer === 'pdf'"
          :src="temp.linkPreview"
          width="1440px"
          :height="`${clientHeight - 120}px`"
        />
      </div>
    </template>
  </Modal>
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
  import Modal from '~/components/atoms/Modal.vue'
  import { FileViewerType } from '~/types/components/atoms/forms/header/file'
  import { Notyf, t } from '~/services'
  import axios from 'axios'
  import { Lang } from '~/types/form/form-v1'

  defineComponent({
    name: 'Popupfield',
  })

  const host = import.meta.env.VITE_APP_API_HOST
  const props = defineProps({
    name: {
      type: String,
      required: true,
    },
    viewer: {
      type: String as () => FileViewerType,
      required: true,
    },
    modelValue: {
      type: [File, String],
      default: null,
    },
    outVal: {
      type: [File, String],
      default: null,
    },
    hasChanged: {
      type: Boolean,
      required: true,
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
    default: {
      type: String,
      default: null,
    },
    autoUpload: {
      type: Boolean,
      default: false,
    },
    linkPreview: {
      type: String,
      default: null,
    },
    linkResponse: {
      type: String,
      default: null,
    },
  })

  const clientWidth = inject('clientWidth')
  const clientHeight = inject('clientHeight')
  const emit = defineEmits([
    'update:modelValue',
    'update:ready',
    'update:hasChanged',
    'update:linkPreview',
    'update:linkResponse',
  ])
  const picker = ref()
  const isReady = ref(false)
  const nativeEdit = ref(false)
  const temp = reactive({
    value_: null,
    displayValue: '',
    onchange: false,
    showModal: false,
    value: computed({
      get: () => props.modelValue,
      set: (val) => emit('update:modelValue', val),
    }),
    ready: computed({
      get: () => props.ready,
      set: (val) => emit('update:ready', val),
    }),
    hasChanged: computed({
      get: () => props.hasChanged,
      set: (val) => emit('update:hasChanged', val),
    }),
    linkPreview: computed({
      get: () => props.linkPreview,
      set: (val) => emit('update:linkPreview', val),
    }),
    linkResponse: computed({
      get: () => props.linkResponse,
      set: (val) => emit('update:linkResponse', val),
    }),
  })

  const methods = {
    onPickFile: () => {
      picker.value.click()
    },
    onReaderBase64: (file: any) => {
      temp.displayValue = file.name
      return new Promise((resolve) => {
        const reader = new FileReader()
        reader.onload = (e) => {
          temp.value = file
          temp.onchange = true
          temp.linkPreview = e.target?.result as string
          resolve('done')
          setTimeout(() => {
            nativeEdit.value = false
          }, 100)
        }
        reader.readAsDataURL(file)
      })
    },
    onChange: async (event: any) => {
      nativeEdit.value = true
      const rules: Array<string> = props.properties[props.name].rules
      let hasError = false
      for (let i = 0; i < rules.length; i++) {
        const rule = rules[i]
        if (rule.indexOf('mimes:') > -1) {
          let types: any = rule.replace('mimes:', '')
          types = types.split(',')
          let cType = event.target.files[0].type
            .replace(
              'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
              'xlsx',
            )
            .replace('application/vnd.ms-excel', 'xls')
            .replace(
              'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
              'docx',
            )
            .replace('application/msword', 'doc')
            .replace('application/pdf', 'pdf')

          let match = 0
          types.forEach((type) => {
            if (cType.indexOf(type) > -1) {
              match++
            }
          })

          if (!match) {
            const expected = rule.replace('mimes:', '')
            const actual = cType
            hasError = true
            Notyf({
              type: 'error',
              message: `Expected ${expected} file, but got ${actual} file`,
              duration: 3000,
              ripple: true,
              dismissible: true,
              position: {
                x: 'right',
                y: 'top',
              },
            })
          }
        }
      }

      await methods.onReaderBase64(event.target.files[0])
      temp.hasChanged = true

      if (!hasError && props.autoUpload) {
        const host = import.meta.env.VITE_APP_API_HOST
        const file = event.target.files[0]
        const formData = new FormData()
        formData.append('file', file)
        axios({
          url: `${host}/api/upload`,
          method: 'POST',
          headers: {
            'Content-Type': 'multipart/form-data',
          },
          data: formData,
        }).then((res) => {
          temp.linkResponse = res.data.replace(host, '')
        })
      }

      if (props.listener) {
        props.listener(props.properties, 'change', temp.value)
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
      temp.showModal = true
    },
    onCloseModal: () => {
      temp.showModal = false
    },
    onClear: () => {
      nativeEdit.value = true
      temp.linkPreview = ''
      temp.displayValue = ''
      temp.value = null
      picker.value.value = null
      setTimeout(() => {
        nativeEdit.value = false
      }, 100)
    },
    onSetDisplayValue: () => {
      return new Promise((resolve) => {
        resolve(true)
      })
    },
  }

  onMounted(() => {
    if (props.fromGenerator) {
      setTimeout(() => {
        if (temp.value) {
          methods.onSetDisplayValue().then(() => {
            isReady.value = true
            temp.ready++
          })
        } else {
          isReady.value = true
          temp.ready++
        }
      }, 100)
    }
  })

  watch(
    () => props.outVal,
    () => {
      if (!nativeEdit.value && temp.value !== props.outVal) {
        const imgPath = props.outVal
        if (typeof imgPath === 'string' && imgPath) {
          temp.linkPreview = host + imgPath
          temp.displayValue = imgPath.replace('/storage/uploads/files/', '')
          temp.linkResponse = imgPath
        }
      }
    },
  )

  const code = ref('')
  if (props.viewer === 'image') {
    code.value = `
    {
      type: 'filefield',
      value: '',
      col: 'col-span-12 lg:col-span-6',
      options: {
        label: 'Upload Image',
        information: 'Help text example: Upload Image',
        inline: true,
        disabled: false,
        hidden: false,
        helpText: 'Only png, jpg or jpeg file'
      },
      listener: (element, type, value) => {
        // console.log(type, value)
      },
      rules: ['required','mimes:jpg,png,jpeg,webp']
    }
  `
  } else if (props.viewer === 'pdf') {
    code.value = `
    {
      type: 'filefield',
      value: '',
      col: 'col-span-12 lg:col-span-6',
      options: {
        label: 'Upload PDF',
        information: 'Help text example: Upload PDF',
        inline: true,
        disabled: false,
        hidden: false,
        helpText: 'Only pdf file'
      },
      listener: (element, type, value) => {
        // console.log(type, value)
      },
      rules: ['required','mimes:pdf']
    }
  `
  }
</script>
