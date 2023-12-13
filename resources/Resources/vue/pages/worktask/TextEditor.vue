<template>
  <div :class="rootClass">
    <div v-if="mode === 'BalloonEditor'">
      <div
        :style="{
          height: isExpanded ? 'auto' : partHeight,
          overflow: isExpanded ? 'auto' : 'hidden',
        }"
        :class="{
          'hover:border-b hover:border-base-200': !isExpanded,
        }"
      >
        <div :id="elementId + '-child'">
          <span v-html="tempVal.value_"></span>
        </div>
      </div>
      <div
        v-if="!isExpanded"
        :style="{
          height: '50px',
          position: 'relative',
          margin: '-51px 1px 0',
          display: 'flex',
          justifyContent: 'center',
          alignItems: 'flex-end',
          background:
            theme === 'light'
              ? 'linear-gradient(to top, rgb(255, 255, 255) 0%, rgba(255, 255, 255, 0.4) 95%, rgba(255, 255, 255, 0) 100%)'
              : 'linear-gradient(to top, rgb(61, 68, 81) 0%, rgba(61, 68, 81, 0.4) 95%, rgba(61, 68, 81, 0) 100%)',
          cursor: 'pointer',
          zIndex: 9,
        }"
        @click="methods.onToggleExpand"
      >
        <div
          style="margin-bottom: -15px; z-index: 10"
          class="rounded-2xl text-xs font-bold shadow border border-base-200 bg-base-100 hover:bg-base-200 cursor-pointer px-3 py-1"
          @click="methods.onToggleExpand"
        >
          <span v-if="!isExpanded">
            <i class="fa-regular fa-chevron-down mr-2"></i> See More
          </span>
          <span v-else>
            <i class="fa-regular fa-chevron-up mr-2"></i> See Less
          </span>
        </div>
      </div>
      <div v-else class="flex justify-center">
        <div
          style="margin-top: -13px"
          class="rounded-2xl text-xs font-bold shadow border border-base-200 bg-base-100 hover:bg-base-200 cursor-pointer px-3 py-1"
          @click="methods.onToggleExpand"
        >
          <span v-if="!isExpanded">
            <i class="fa-regular fa-chevron-down mr-2"></i> See More
          </span>
          <span v-else>
            <i class="fa-regular fa-chevron-up mr-2"></i> See Less
          </span>
        </div>
      </div>
    </div>
    <textarea
      v-else
      :id="elementId + '-child'"
      v-model="tempVal.value_"
    ></textarea>
  </div>
</template>

<script setup lang="ts">
  import {
    computed,
    defineEmits,
    defineProps,
    inject,
    onMounted,
    reactive,
    ref,
  } from 'vue'
  import { Lang } from '~/types/form/form-v1'
  import '~/assets/css/custom.ckeditor5.css'
  import { useTempsStore } from '~/store/temps'
  import { Ref, UnwrapRef } from '@vue/reactivity'

  let CKEDITOR
  const props = defineProps({
    modelValue: {
      type: String,
      default: '',
    },
    elementId: {
      type: String,
      required: true,
    },
    rootClass: {
      type: String,
      default: '',
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
          shouldNotGroupWhenFull: false,
        }
      },
    },
    height: {
      type: String,
      default: '600px',
    },
    mode: {
      type: String,
      default: 'ClassicEditor',
    },
  })

  const tempsStore = useTempsStore()
  const emit = defineEmits(['update:modelValue'])
  const tempVal = reactive({
    value_: '',
    value: computed({
      get: () => props.modelValue,
      set: (val) => emit('update:modelValue', val),
    }),
  })

  tempVal.value_ = tempVal.value
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

  let editor
  const theme = inject('theme') as Ref<UnwrapRef<string>>
  let partHeight = ref('300px')
  let isExpanded = ref(false)
  let isExpanding = ref(false)
  const methods = {
    onToggleExpand: () => {
      if (!isExpanding.value) {
        isExpanding.value = true
      } else {
        return
      }

      isExpanded.value = !isExpanded.value
      if (isExpanded.value) {
        editor.disableReadOnlyMode(`#${props.elementId}-child`)
      } else {
        editor.enableReadOnlyMode(`#${props.elementId}-child`)
      }

      setTimeout(() => {
        isExpanding.value = false
      }, 200)
    },
  }
  onMounted(async () => {
    CKEDITOR = window['CKEDITOR']
    CKEDITOR[props.mode]
      .create(document.querySelector(`#${props.elementId}-child`), editorConfig)
      .then((newEditor) => {
        editor = newEditor
        if (props.mode === 'BalloonEditor') {
          editor.enableReadOnlyMode(`#${props.elementId}-child`)
        }

        if (props.disabled) {
          editor.enableReadOnlyMode(`#${props.elementId}-child`)
        }

        editor.editing.view.document.on('change', () => {
          tempVal.value = editor.getData()
        })

        editor.editing.view.document.on('focus', () => {
          tempsStore.setIgnoreClickOutsideModal(true)
        })

        editor.editing.view.document.on('blur', () => {
          setTimeout(() => {
            tempsStore.setIgnoreClickOutsideModal(false)
          }, 100)
        })
      })
      .catch((error) => {
        console.error(error)
      })
  })
</script>
<style>
  .text-editor-modal-woktask-left
    .ck-restricted-editing_mode_standard.ck.ck-content.ck-editor__editable.ck-rounded-corners.ck-editor__editable_inline.ck-blurred {
    @apply hover:border-base-200;
  }

  .text-editor-modal-woktask-right .ck.ck-editor__main > .ck.ck-content {
    height: 190px !important;
  }
</style>
