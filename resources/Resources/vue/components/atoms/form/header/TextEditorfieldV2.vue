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
          <textarea :id="props.name" v-model="temp.value"></textarea>
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
  import '~/assets/css/custom.ckeditor5.css'
  import { Highlight } from '~/components/atoms'
  import { Notyf, t } from '~/services'
  import { Lang } from '~/types/form/form-v1'

  let CKEDITOR
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
      type: Object,
      default: () => {
        return {
          items: [
            'undo',
            'redo',
            '|',
            'heading',
            '|',
            'fontFamily',
            'fontSize',
            'fontColor',
            'fontBackgroundColor',
            '|',
            'bold',
            'italic',
            'underline',
            'strikethrough',
            'code',
            'subscript',
            'superscript',
            '|',
            'link',
            'uploadImage',
            'insertTable',
            'blockQuote',
            'htmlEmbed',
            'codeBlock',
            'highlight',
            'horizontalLine', //
            'mediaEmbed',
            'pageBreak',
            '|',
            'alignment',
            '|',
            'bulletedList',
            'numberedList',
            'outdent',
            'indent',
            '|',
            'sourceEditing',
            'findAndReplace',
          ],
          table: {
            contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells'],
          },
          shouldNotGroupWhenFull: true,
        }
      },
    },
  })

  const clientWidth = inject('clientWidth')
  const emit = defineEmits(['update:modelValue', 'update:ready'])
  const temp = reactive({
    value_: '',
    value: computed({
      get: () => props.modelValue,
      set: (val) => emit('update:modelValue', val),
    }),
    ready: computed({
      get: () => props.ready,
      set: (val) => emit('update:ready', val),
    }),
    editorConfig: {
      removePlugins: [
        'Markdown',
        // These two are commercial, but you can try them out without registering to a trial.
        // 'ExportPdf',
        // 'ExportWord',
        'CKBox',
        'CKFinder',
        'EasyImage',
        // This sample uses the Base64UploadAdapter to handle image uploads as it requires no configuration.
        // https://ckeditor.com/docs/ckeditor5/latest/features/images/image-upload/base64-upload-adapter.html
        // Storing images as Base64 is usually a very bad idea.
        // Replace it on production website with other solutions:
        // https://ckeditor.com/docs/ckeditor5/latest/features/images/image-upload/image-upload.html
        // 'Base64UploadAdapter',
        'RealTimeCollaborativeComments',
        'RealTimeCollaborativeTrackChanges',
        'RealTimeCollaborativeRevisionHistory',
        'PresenceList',
        'Comments',
        'TrackChanges',
        'TrackChangesData',
        'RevisionHistory',
        'Pagination',
        'WProofreader',
        // Careful, with the Mathtype plugin CKEditor will not load when loading this sample
        // from a local file system (file://) - load this site via HTTP server if you enable MathType.
        'MathType',
        // The following features are part of the Productivity Pack and require additional license.
        'SlashCommand',
        'Template',
        'DocumentOutline',
        'FormatPainter',
        'TableOfContents',
      ],
      toolbar: props.toolbars,
      list: {
        properties: {
          styles: true,
          startIndex: true,
          reversed: true,
        },
      },
      // https://ckeditor.com/docs/ckeditor5/latest/features/headings.html#configuration
      heading: {
        options: [
          {
            model: 'paragraph',
            title: 'Paragraph',
            class: 'ck-heading_paragraph',
          },
          {
            model: 'heading1',
            view: 'h1',
            title: 'Heading 1',
            class: 'ck-heading_heading1',
          },
          {
            model: 'heading2',
            view: 'h2',
            title: 'Heading 2',
            class: 'ck-heading_heading2',
          },
          {
            model: 'heading3',
            view: 'h3',
            title: 'Heading 3',
            class: 'ck-heading_heading3',
          },
          {
            model: 'heading4',
            view: 'h4',
            title: 'Heading 4',
            class: 'ck-heading_heading4',
          },
          {
            model: 'heading5',
            view: 'h5',
            title: 'Heading 5',
            class: 'ck-heading_heading5',
          },
          {
            model: 'heading6',
            view: 'h6',
            title: 'Heading 6',
            class: 'ck-heading_heading6',
          },
        ],
      },
      // https://ckeditor.com/docs/ckeditor5/latest/features/editor-placeholder.html#using-the-editor-configuration
      placeholder: props.placeholder,
      // https://ckeditor.com/docs/ckeditor5/latest/features/font.html#configuring-the-font-family-feature
      fontFamily: {
        options: [
          'default',
          'Arial, Helvetica, sans-serif',
          'Courier New, Courier, monospace',
          'Georgia, serif',
          'Lucida Sans Unicode, Lucida Grande, sans-serif',
          'Tahoma, Geneva, sans-serif',
          'Times New Roman, Times, serif',
          'Trebuchet MS, Helvetica, sans-serif',
          'Verdana, Geneva, sans-serif',
        ],
        supportAllValues: true,
      },
      // https://ckeditor.com/docs/ckeditor5/latest/features/font.html#configuring-the-font-size-feature
      fontSize: {
        options: [10, 12, 14, 'default', 18, 20, 22],
        supportAllValues: true,
      },
      simpleUpload: {
        // The URL that the images are uploaded to.
        uploadUrl: '/api/upload',

        // Enable the XMLHttpRequest.withCredentials property.
        withCredentials: true,

        // Headers sent along with the XMLHttpRequest to the upload server.
        headers: {
          'X-CSRF-TOKEN': 'CSRF-Token',
          Authorization: localStorage.getItem('_token'),
        },
      },
      ui: {
        poweredBy: {
          forceVisible: false,
        },
      },
    },
  })

  const nativeEdit = ref(false)

  const isReady = ref(false)
  let editor
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

      temp.value = evt.value

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
    CKEDITOR = window['CKEDITOR']
    CKEDITOR.ClassicEditor.create(
      document.querySelector(`#${props.name}`),
      temp.editorConfig,
    )
      .then((newEditor) => {
        setTimeout(() => {
          temp.ready++
        }, 500)
        editor = newEditor
        if (props.disabled) {
          editor.enableReadOnlyMode(`#${props.name}`)
        }

        editor.editing.view.document.on('change', () => {
          methods.onChange({ type: 'change', value: editor.getData() })
        })

        editor.editing.view.document.on('keypress', () => {
          methods.onChange({ type: 'keypress', value: editor.getData() })
        })

        editor.editing.view.document.on('keyup', () => {
          methods.onChange({ type: 'keyup', value: editor.getData() })
        })

        editor.editing.view.document.on('keydown', (evt, data) => {
          methods.onChange({ type: 'down', value: editor.getData() })
        })

        editor.editing.view.document.on('paste', () => {
          methods.onChange({ type: 'paste', value: editor.getData() })
        })

        editor.editing.view.document.on('focus', () => {
          methods.onChange({ type: 'focus', value: editor.getData() })
        })

        editor.editing.view.document.on('blur', () => {
          methods.onChange({ type: 'blur', value: editor.getData() })
        })
      })
      .catch((error) => {
        console.error(error)
      })
  })

  watch(
    () => props.disabled,
    () => {
      if (props.disabled) {
        editor.enableReadOnlyMode(`#${props.name}`)
      } else {
        editor.disableReadOnlyMode(`#${props.name}`)
      }
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
        editor.setData(outVal)
      } else {
        temp.value = ''
        editor.setData('')
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
      version: 2,
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
