<!--suppress JSJQueryEfficiency -->
<template>
  <div class="grid grid-cols-12">
    <div :class="{ 'col-span-6': docs, 'col-span-12': !docs }">
      <div :class="{ flex: true, 'flex-column': !inline }">
        <div
          :style="{
            minWidth: inline ? (clientWidth < 640 ? 'unset' : '150px') : '',
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
          <textarea
            :id="name"
            v-model="temp.value"
            :class="{
              'block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 disabled:bg-gray-200 disabled:text-gray-400 dark:disabled:text-gray-400 dark:disabled:bg-gray-600': true,
            }"
            rows="4"
            :placeholder="t(placeholder)"
            :disabled="disabled"
            :readonly="readonly"
          >
          </textarea>
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
  import '~/assets/css/custom.summernote-lite.css'
  import { Highlight } from '~/components/atoms'
  import { toolbarDef } from '~/types/components/atoms/forms/header/texteditor/v1'
  import { Notyf, t } from '~/services'
  import { Lang } from '~/types/form/form-v1'

  defineComponent({
    name: 'TextEditorfield',
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
    toolbars: {
      type: Array as () => toolbarDef,
      default: () => {
        return [
          ['style', ['style', 'bold', 'italic', 'underline', 'clear']],
          [
            'font',
            [
              'fontname',
              'fontsize',
              'fontsizeunit',
              'color',
              'forecolor',
              'backcolor',
              'strikethrough',
              'superscript',
              'subscript',
            ],
          ],
          ['para', ['ul', 'ol', 'paragraph', 'height']],
          ['table', ['table']],
          ['insert', ['link', 'picture', 'video', 'hr']],
          ['misc', ['fullscreen', 'codeview', 'undo', 'redo', 'help']],
        ]
      },
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

  const isFocus = ref(false)
  const isReady = ref(false)
  const methods = {
    onChange: (evt) => {
      if (
        evt.type === 'change' ||
        evt.type === 'keyup' ||
        evt.type === 'keydown' ||
        evt.type === 'paste'
      ) {
        nativeEdit.value = true
      }

      if (evt.type === 'change') {
        temp.value =
          evt.value === '<p><br></p>' || evt.value === '<br>' ? '' : evt.value
        if (props.listener) {
          props.listener(props.properties, evt.type, temp.value)
        }
      } else {
        props.listener(props.properties, evt.type, evt.value)
      }

      if (
        evt.type === 'change' ||
        evt.type === 'keyup' ||
        evt.type === 'keydown' ||
        evt.type === 'paste'
      ) {
        setTimeout(() => {
          nativeEdit.value = false
        }, 100)
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
    setTimeout(() => {
      $(`#${props.name}`).summernote({
        placeholder: t(props.placeholder),
        tabsize: 2,
        height: 150,
        dialogsInBody: true,
        toolbar: props.toolbars,
        callbacks: {
          onChange: function (contents) {
            methods.onChange({ type: 'change', value: contents })
          },
          onEnter: function () {
            methods.onChange({ type: 'enter' })
          },
          onFocus: function () {
            $(`#${props.name} ~ .note-editor.note-frame`).removeClass(
              'border-unfocus',
            )
            $(`#${props.name} ~ .note-editor.note-frame`).removeClass(
              'border-focus',
            )
            $(`#${props.name} ~ .note-editor.note-frame`).removeClass(
              'border-error',
            )
            if (
              props.properties.errors &&
              props.properties.errors(props.name)
            ) {
              $(`#${props.name} ~ .note-editor.note-frame`).addClass(
                'border-error',
              )
            } else {
              $(`#${props.name} ~ .note-editor.note-frame`).addClass(
                'border-focus',
              )
            }
            isFocus.value = true
            methods.onChange({ type: 'focus' })
          },
          onBlur: function () {
            $(`#${props.name} ~ .note-editor.note-frame`).removeClass(
              'border-unfocus',
            )
            $(`#${props.name} ~ .note-editor.note-frame`).removeClass(
              'border-focus',
            )
            $(`#${props.name} ~ .note-editor.note-frame`).removeClass(
              'border-error',
            )
            if (
              props.properties.errors &&
              props.properties.errors(props.name)
            ) {
              $(`#${props.name} ~ .note-editor.note-frame`).addClass(
                'border-error',
              )
            } else {
              $(`#${props.name} ~ .note-editor.note-frame`).addClass(
                'border-unfocus',
              )
            }
            isFocus.value = false
            methods.onChange({ type: 'blur' })
          },
          onInit: function () {
            $(`#${props.name} ~ .note-editor.note-frame`).addClass(
              'border-unfocus',
            )
            $(
              `#${props.name} ~ .note-editor .note-editing-area .note-editable`,
            ).addClass('note-editable-default')
            $(`#${props.name} ~ .note-editor .note-placeholder`).addClass(
              'note-placeholder-default',
            )
            isReady.value = true
            if (props.default) {
              temp.value = props.default
              $(`#${props.name}`).summernote('code', props.default)
            }
            temp.ready++
            setTimeout(() => {
              $(`.note-modal-footer`).removeClass('btn btn-primary')
              $(`.note-color-reset`).removeClass('btn btn-light btn-default')
              $(`.note-color-select`).removeClass('btn btn-light btn-default')
            }, 1000)
          },
          onKeyup: function (e) {
            methods.onChange({
              type: 'keyup',
              value: { keyCode: e.keyCode, key: e.key },
            })
          },
          onKeydown: function (e) {
            methods.onChange({
              type: 'keydown',
              value: { keyCode: e.keyCode, key: e.key },
            })
          },
          onPaste: function () {
            methods.onChange({ type: 'paste' })
          },
        },
      })

      $(`#${props.name}`).summernote(
        props.disabled === true ? 'disable' : 'enable',
      )
    }, 1000)
  })

  watch(
    () => props.disabled,
    () => {
      $(`#${props.name}`).summernote(
        props.disabled === true ? 'disable' : 'enable',
      )
    },
    {
      deep: true,
    },
  )

  // Only for custom style
  watch(
    props.properties,
    () => {
      $(`#${props.name} ~ .note-editor.note-frame`).removeClass(
        'border-unfocus',
      )
      $(`#${props.name} ~ .note-editor.note-frame`).removeClass('border-focus')
      $(`#${props.name} ~ .note-editor.note-frame`).removeClass('border-error')
      $(
        `#${props.name} ~ .note-editor .note-editing-area .note-editable`,
      ).removeClass('note-editable-default')
      $(
        `#${props.name} ~ .note-editor .note-editing-area .note-editable`,
      ).removeClass('note-editable-error')
      $(`#${props.name} ~ .note-editor .note-placeholder`).removeClass(
        'note-placeholder-default',
      )
      $(`#${props.name} ~ .note-editor .note-placeholder`).removeClass(
        'note-placeholder-error',
      )
      if (props.properties.errors && props.properties.errors(props.name)) {
        $(`#${props.name} ~ .note-editor.note-frame`).addClass('border-error')
        $(
          `#${props.name} ~ .note-editor .note-editing-area .note-editable`,
        ).addClass('note-editable-error')
        $(`#${props.name} ~ .note-editor .note-placeholder`).addClass(
          'note-placeholder-error',
        )
      } else if (isFocus.value) {
        $(`#${props.name} ~ .note-editor.note-frame`).addClass('border-focus')
        $(
          `#${props.name} ~ .note-editor .note-editing-area .note-editable`,
        ).addClass('note-editable-default')
        $(`#${props.name} ~ .note-editor .note-placeholder`).addClass(
          'note-placeholder-default',
        )
      } else {
        $(`#${props.name} ~ .note-editor.note-frame`).addClass('border-unfocus')
        $(
          `#${props.name} ~ .note-editor .note-editing-area .note-editable`,
        ).addClass('note-editable-default')
        $(`#${props.name} ~ .note-editor .note-placeholder`).addClass(
          'note-placeholder-default',
        )
      }

      // const outVal = props.properties[props.name].outVal
      // if (parentReady.value && !nativeEdit.value && temp.value !== outVal) {
      // }
    },
    {
      deep: true,
    },
  )

  const runUpdater = () => {
    const outVal = props.properties[props.name].outVal
    if (!nativeEdit.value && temp.value !== outVal) {
      if (outVal) {
        temp.value = outVal
        $(`#${props.name}`).summernote('code', outVal)
      } else {
        temp.value = ''
        $(`#${props.name}`).summernote('code', '')
      }
    }
  }

  watch(
    () => props.outVal,
    () => {
      const outVal = props.properties[props.name].outVal
      if (isReady.value) {
        runUpdater()
      } else {
        setTimeout(() => {
          runUpdater()
        }, 1000)
      }
    },
  )

  const code = ref(`
    {
      type: 'texteditorfield',
      version: 1,
      value: '',
      col: 'col-span-12',
      options: {
        label: 'Text Editor',
        information: 'Help text example: Text Editor',
        inline: true,
        placeholder: 'Write a text here',
        disabled: false,
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
