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
            'cursor-not-allowed bg-gray-50 border border-gray-300 text-gray-900 text-sm focus:ring-blue-500 focus:outline-none focus:border-blue-500 block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 disabled:bg-gray-200 disabled:text-gray-400 dark:disabled:text-gray-400 dark:disabled:bg-gray-600': true,
            'bg-red-50 border border-red-500 text-red-900 placeholder-red-700 dark:placeholder-red-700 dark:text-red-700 text-sm focus:ring-red-500 focus:outline-none focus:border-red-500 block w-full p-1.5 dark:bg-red-100 dark:border-red-400':
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
          :placeholder="placeholder"
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
          v-if="tempVal.linkPreview && (viewer === 'image' || viewer === 'pdf')"
          class="btn-secondary border border-0 cursor-pointer flex justify-center items-center px-0"
          :style="{
            minWidth: '32px',
            maxWidth: '32px',
            'border-top-right-radius':
              !clearable || !tempVal.displayValue || disabled ? 'inherit' : '',
            'border-bottom-right-radius':
              !clearable || !tempVal.displayValue || disabled ? 'inherit' : '',
          }"
          @click="methods.onShowModal"
        >
          <i :class="`fal fa-file-${viewer}`"></i>
        </button>
        <button
          v-else-if="!disabled"
          disabled="disabled"
          class="btn-primary text-white border border-0 cursor-pointer flex justify-center items-center px-0"
          :style="{
            minWidth: '32px',
            maxWidth: '32px',
            'border-top-right-radius':
              !clearable || !tempVal.displayValue ? 'inherit' : '',
            'border-bottom-right-radius':
              !clearable || !tempVal.displayValue ? 'inherit' : '',
          }"
          @click="methods.onPickFile"
        >
          <i class="fal fa-folder-open"></i>
        </button>
        <button
          v-if="clearable && tempVal.displayValue && !disabled"
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

  <Modal v-model="tempVal.showModal" width="max-w-5xl">
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
          :src="tempVal.linkPreview"
          alt=""
          style="max-width: 650px; object-fit: scale-down"
        />
        <embed
          v-else-if="viewer === 'pdf'"
          :src="tempVal.linkPreview"
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
    defineProps,
    inject,
    onMounted,
    reactive,
    ref,
    Ref,
    UnwrapRef,
    watch,
  } from 'vue'
  import FormValidation from './Validation.vue'
  import { Element } from '~/types/components/atoms/forms/detail'
  import { Axios, Notyf, ObjectReader, ObjectUpdater, t } from '~/services'
  import Modal from '~/components/atoms/Modal.vue'
  import { FileViewerType } from '~/types/components/atoms/forms/header/file'
  import { FormSubDetail } from '~/types/form/subdetail'
  import { Lang } from '~/types/form/form-v1'

  defineComponent({
    name: 'FilefieldDetail',
  })

  const host = import.meta.env.VITE_APP_API_HOST
  const props = defineProps({
    rowIndex: {
      type: Number,
      required: true,
    },
    viewer: {
      type: String as () => FileViewerType,
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
    default: {
      type: String,
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
    autoUpload: {
      type: Boolean,
      default: false,
    },
    rules: {
      type: Array as () => Array<string>,
      default: () => {
        return []
      },
    },
  })

  const form = inject('form') as FormSubDetail
  const clientHeight = inject('clientHeight')
  const element = inject('element') as Element
  const picker = ref()
  const tempValParent = inject('tempValParent') as Ref<UnwrapRef<Array<any>>>
  const tempVal = reactive({
    value_: null,
    displayValue: '',
    onchange: false,
    showModal: false,
    hasChanged: false,
    linkPreview: '',
    value: computed({
      get: () =>
        ObjectReader(tempValParent.value[props.rowIndex], props.field) || '',
      set: (val) =>
        ObjectUpdater(tempValParent.value[props.rowIndex], props.field, val),
    }),
  })

  // set default
  if (!tempVal.value) {
    tempVal.value = props.default
  }

  const elementId = ref(
    `${props.uniqueId}-${props.field}-${props.rowIndex}`.replace('.', '-'),
  )

  const nativeEdit = ref(false)
  const methods = {
    onPickFile: () => {
      picker.value.click()
    },
    onReaderBase64: (file: any) => {
      tempVal.displayValue = file.name
      return new Promise((resolve) => {
        const reader = new FileReader()
        reader.onload = (e) => {
          tempVal.value = file
          tempVal.onchange = true
          tempVal.linkPreview = e.target?.result as string
          resolve('done')
          setTimeout(() => {
            nativeEdit.value = false
          }, 100)
        }
        reader.readAsDataURL(file)
      })
    },
    onChange: async (event) => {
      nativeEdit.value = true
      const rules: Array<string> = props.rules
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
      tempVal.hasChanged = true

      if (!hasError && props.autoUpload) {
        const host = import.meta.env.VITE_APP_API_HOST
        const file = event.target.files[0]
        const formData = new FormData()
        formData.append('file', file)
        Axios({
          url: `${host}/api/upload`,
          method: 'POST',
          headers: {
            'Content-Type': 'multipart/form-data',
          },
          data: formData,
        }).then((res: any) => {
          tempVal.value = res.data.replace(host, '')
        })
      }

      if (props.listener) {
        props.listener(element, 'change', tempVal.value)
      }
    },
    onShowModal: () => {
      tempVal.showModal = true
    },
    onCloseModal: () => {
      if (tempVal.showModal) {
        tempVal.showModal = false
      }
    },
    onClear: () => {
      nativeEdit.value = true
      tempVal.linkPreview = ''
      tempVal.displayValue = ''
      tempVal.value = null
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
    form.ready++
  })

  watch(tempVal, () => {
    if (tempValParent.value[props.rowIndex] && !nativeEdit.value) {
      const imgPath = ObjectReader(
        tempValParent.value[props.rowIndex],
        props.field,
      )
      if (typeof imgPath === 'string' && imgPath) {
        tempVal.linkPreview = host + imgPath
        tempVal.displayValue = imgPath.replace('/storage/uploads/files/', '')
        tempVal.value = imgPath
      }
    }
  })
</script>
