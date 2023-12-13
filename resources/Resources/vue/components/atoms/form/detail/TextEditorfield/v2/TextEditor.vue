<!--suppress JSJQueryEfficiency -->
<template>
  <div class="grid-cols-12 px-2.5 pt-5">
    <div
      :class="{
        'col-span-12': true,
        hidden: !isReady,
      }"
    >
      <textarea :id="elementId + '-child'" v-model="tempVal.value_"></textarea>
    </div>
    <div
      :class="{
        'col-span-12 flex justify-center items-center': true,
        hidden: isReady,
      }"
      style="height: 243px"
    >
      <progress class="progress w-56"></progress>
    </div>
  </div>
</template>

<script setup lang="ts">
  import { inject, onMounted, ref, defineProps, reactive } from 'vue'
  import { Lang } from '~/types/form/form-v1'
  import '~/assets/css/custom.ckeditor5.css'
  import { stripHtml } from 'string-strip-html'

  let CKEDITOR
  const props = defineProps({
    elementId: {
      type: String,
      required: true,
    },
    placeholder: {
      type: [String, Object as () => Lang],
      default: '',
    },
    disabled: {
      type: Boolean,
      default: false,
    },
    readonly: {
      type: Boolean,
      default: false,
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

  const tempVal = inject('tempVal') as {
    displayValue: string
    displayValue_: string
    value_: string
    showModal: boolean
    value: string
  }

  const editorConfig = reactive({
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
  })

  tempVal.value_ = tempVal.value
  const isReady = ref(false)
  let editor

  onMounted(() => {
    CKEDITOR = window['CKEDITOR']
    CKEDITOR.ClassicEditor.create(
      document.querySelector(`#${props.elementId}-child`),
      editorConfig,
    )
      .then((newEditor) => {
        setTimeout(() => {
          isReady.value = true
        }, 500)
        editor = newEditor
        if (props.disabled) {
          editor.enableReadOnlyMode(`#${props.elementId}-child`)
        }

        editor.editing.view.document.on('change', () => {
          tempVal.value_ = editor.getData()
          tempVal.displayValue_ = stripHtml(editor.getData()).result
          // methods.onChange({ type: 'change', value: editor.getData() })
        })

        editor.editing.view.document.on('keypress', () => {
          tempVal.value_ = editor.getData()
          tempVal.displayValue_ = stripHtml(editor.getData()).result
          // methods.onChange({ type: 'keypress', value: editor.getData() })
        })

        editor.editing.view.document.on('keyup', () => {
          tempVal.value_ = editor.getData()
          tempVal.displayValue_ = stripHtml(editor.getData()).result
          // methods.onChange({ type: 'keyup', value: editor.getData() })
        })

        editor.editing.view.document.on('keydown', (evt, data) => {
          tempVal.value_ = editor.getData()
          tempVal.displayValue_ = stripHtml(editor.getData()).result
          // methods.onChange({ type: 'down', value: editor.getData() })
        })

        editor.editing.view.document.on('paste', () => {
          tempVal.value_ = editor.getData()
          tempVal.displayValue_ = stripHtml(editor.getData()).result
          // methods.onChange({ type: 'paste', value: editor.getData() })
        })

        editor.editing.view.document.on('focus', () => {
          tempVal.value_ = editor.getData()
          tempVal.displayValue_ = stripHtml(editor.getData()).result
          // methods.onChange({ type: 'focus', value: editor.getData() })
        })

        editor.editing.view.document.on('blur', () => {
          tempVal.value_ = editor.getData()
          tempVal.displayValue_ = stripHtml(editor.getData()).result
          // methods.onChange({ type: 'blur', value: editor.getData() })
        })
      })
      .catch((error) => {
        console.error(error)
      })
  })
</script>
