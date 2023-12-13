<template>
  <div>
    <div class="w-full items-center select2-detail">
      <div class="flex px-0 rounded-lg">
        <input
          :id="elementId"
          v-model="tempVal.displayValue"
          autocomplete="off"
          type="text"
          :class="{
            'cursor-not-allowed bg-gray-50 border border-gray-300 text-gray-900 text-sm focus:ring-blue-500 focus:outline-none focus:border-blue-500 block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 disabled:bg-gray-200 disabled:text-gray-400 dark:disabled:text-gray-400 dark:disabled:bg-gray-600': true,
            'bg-red-50 border border-red-500 text-red-900  placeholder-red-700 dark:placeholder-red-700 dark:text-red-700 text-sm focus:ring-red-500 focus:outline-none focus:border-red-500 block w-full p-1.5 dark:bg-red-100 dark:border-red-400':
              temp.form.rows[rowIndex].errors &&
              temp.form.rows[rowIndex].errors(temp.form, rowIndex, field),
          }"
          style="
            border-top-left-radius: inherit;
            border-bottom-left-radius: inherit;
          "
          :placeholder="placeholder"
          :disabled="disabled"
          :readonly="true"
        />
        <button
          class="btn-primary text-white border border-0 cursor-pointer flex justify-center items-center px-0"
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
          <i class="fal fa-pencil-alt"></i>
        </button>
        <button
          v-if="clearable && tempVal.displayValue"
          type="button"
          class="btn-error text-white border border-0 cursor-pointer flex justify-center items-center px-0"
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
      :rows="temp.form.rows"
      :row-index="rowIndex"
      :field="field"
      :form="temp.form"
    />
  </div>

  <Modal v-model="tempVal.showModal" width="w-11/12 max-w-5xl">
    <template #modal-title>Text Editor</template>
    <template #modal-body>
      <text-editor
        :element-id="elementId"
        :placeholder="placeholder"
        :disabled="disabled"
        :readonly="readonly"
      />
    </template>
    <template #modal-footer>
      <div class="flex justify-end pt-6 pr-2">
        <button
          type="button"
          class="btn btn-sm btn-primary flex items-center"
          style="text-transform: unset"
          @click="methods.onSubmit"
        >
          <div>Submit</div>
          <div class="ml-1">
            <i class="fas fa-check-circle"></i>
          </div>
        </button>
      </div>
    </template>
  </Modal>
</template>

<script setup lang="ts">
  import { defineComponent, inject, onMounted, ref, defineProps } from 'vue'
  import { stripHtml } from 'string-strip-html'
  import FormValidation from '../../Validation.vue'
  import TextEditor from './TextEditor.vue'
  import { FormDetail } from '~/types/form/detail'
  import { Element } from '~/types/components/atoms/forms/detail'
  import { GetRandomString, ObjectReader, ObjectUpdater, t } from '~/services'
  import { computed, provide, reactive, watch } from 'vue'
  import Modal from '~/components/atoms/Modal.vue'
  import { Lang } from '~/types/form/form-v1'

  defineComponent({
    name: 'TextEditorfieldDetail',
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
    default: {
      type: String,
      default: '',
    },
    clearable: {
      type: Boolean,
      default: false,
    },
  })

  const temp = inject('temp') as {
    form: FormDetail
  }
  const element = inject('element') as Element
  const tempVal = reactive({
    displayValue: '',
    displayValue_: '',
    value_: null,
    showModal: false,
    value: computed({
      get: () => ObjectReader(temp.form.rows[props.rowIndex], props.field),
      set: (val) =>
        ObjectUpdater(temp.form.rows[props.rowIndex], props.field, val),
    }),
  })
  let editor = ref()
  provide('editor', editor)
  provide('tempVal', tempVal)

  const nativeEdit = ref(false)

  // set default
  if (!tempVal.value) {
    tempVal.value = props.default
    tempVal.displayValue = props.default.replace(/<\/?[^>]+(>|$)/g, '')
  } else {
    tempVal.displayValue = tempVal.value.replace(/<\/?[^>]+(>|$)/g, '')
  }

  const uid = GetRandomString(5)
  const elementId = ref(
    `${uid}-${props.field}-${props.rowIndex}`.replace('.', '-'),
  )

  const methods = {
    onChange: (evt) => {
      let fixValue = ''
      if (evt.type === 'change') {
        if (tempVal.value === '<p><br></p>' || tempVal.value === '<br>') {
          fixValue = ''
        } else {
          fixValue = tempVal.value
        }
      } else {
        fixValue = tempVal.value
      }

      if (props.listener) {
        props.listener(element, evt.type, fixValue)
      }
    },
    onShowModal: () => {
      tempVal.showModal = true
      methods.onChange({ type: 'focus' })
    },
    onCloseModal: () => {
      methods.onChange({ type: 'blur' })
    },
    onSubmit: () => {
      nativeEdit.value = true
      tempVal.value = tempVal.value_
      tempVal.displayValue = tempVal.displayValue_
      tempVal.showModal = false
      methods.onChange({ type: 'change' })
      setTimeout(() => {
        nativeEdit.value = false
      }, 300)
    },
    onClear: () => {
      nativeEdit.value = true
      tempVal.displayValue = ''
      tempVal.displayValue_ = ''
      tempVal.value = ''
      tempVal.value_ = ''
      methods.onChange({ type: 'clear' })
      setTimeout(() => {
        nativeEdit.value = false
      }, 300)
    },
  }

  onMounted(() => {
    temp.form.ready++
  })

  watch(
    tempVal,
    () => {
      if (!nativeEdit.value) {
        if (temp.form.rows[props.rowIndex]) {
          tempVal.displayValue = tempVal.value
            ? stripHtml(tempVal.value).result
            : ''
        }
      }
    },
    {
      deep: true,
    },
  )
</script>
