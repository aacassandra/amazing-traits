<!--suppress JSJQueryEfficiency -->
<template>
  <div class="grid-cols-12 px-2.5 pt-5">
    <div
      :class="{
        'col-span-12': true,
        hidden: !isReady,
      }"
    >
      <textarea
        :id="elementId + '-child'"
        v-model="tempVal.value_"
        :class="{
          'block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 disabled:bg-gray-200 disabled:text-gray-400 dark:disabled:text-gray-400 dark:disabled:bg-gray-600': true,
        }"
        rows="4"
        :placeholder="placeholder"
        :disabled="disabled"
        :readonly="readonly"
      >
      </textarea>
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
  import { inject, onMounted, ref, defineProps } from 'vue'
  import { Lang } from '~/types/form/form-v1'

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
  })

  const tempVal = inject('tempVal') as {
    displayValue: string
    displayValue_: string
    value_: string
    showModal: boolean
    value: string
  }

  tempVal.value_ = tempVal.value
  const isReady = ref(false)

  onMounted(() => {
    setTimeout(() => {
      $(`#${props.elementId}-child`).summernote({
        placeholder: props.placeholder,
        tabsize: 2,
        height: 150,
        dialogsInBody: true,
        toolbar: [
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
          ['insert', ['link', 'picture', 'video', 'table', 'hr']],
          ['misc', ['fullscreen', 'codeview', 'undo', 'redo', 'help']],
        ],
        callbacks: {
          onChange: function (contents) {
            tempVal.displayValue_ = $(
              $(`#${props.elementId}-child`).summernote('code'),
            ).text()
            tempVal.value_ = contents
          },
          onInit: function () {
            $('.note-editor.note-frame').addClass('border-unfocus')
            $('.note-editor .note-editing-area .note-editable').addClass(
              'note-editable-default',
            )
            $('.note-placeholder').addClass('note-placeholder-default')
            isReady.value = true
            setTimeout(() => {
              if (process.client) {
                $('.note-modal-footer').removeClass('btn btn-primary')
                $('.note-color-reset').removeClass('btn btn-light btn-default')
                $('.note-color-select').removeClass('btn btn-light btn-default')
              }
            }, 1000)
          },
        },
      })

      $(`#${props.elementId}-child`).summernote(
        props.disabled === true ? 'disable' : 'enable',
      )
    }, 1000)
  })
</script>
