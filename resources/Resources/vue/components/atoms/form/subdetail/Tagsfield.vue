<template>
  <div
    :class="{
      'tagify-dtl': true,
      'is-invalid':
        tempValParent[rowIndex].errors &&
        tempValParent[rowIndex].errors(tempValParent, form, rowIndex, field),
    }"
  >
    <input
      :id="elementId"
      ref="inputRef"
      autocomplete="off"
      type="text"
      :class="{
        hidden: isReady,
        'focus:outline-none': true,
        'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 focus:outline-none block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500': true,
        'text-sm rounded-lg focus:outline-none block w-full p-1.5': true,
        'is-invalid':
          tempValParent[rowIndex].errors &&
          tempValParent[rowIndex].errors(tempValParent, form, rowIndex, field),
      }"
      :placeholder="t(placeholder)"
      :disabled="disabled || false"
      :readonly="readonly || false"
    />
    <form-validation
      :rows="tempValParent"
      :row-index="rowIndex"
      :field="field"
      :parent-editor="parentEditor"
    />
  </div>
</template>

<script setup lang="ts">
  import { defineComponent, inject, onMounted, ref, watch } from 'vue'
  import Tagify from '@yaireo/tagify'
  import { Ref, UnwrapRef } from 'vue'
  import FormValidation from './Validation.vue'
  import { Element } from '~/types/components/atoms/forms/detail'
  import { ObjectReader, ObjectUpdater, t } from '~/services'
  import { computed } from 'vue'
  import { FormSubDetail } from '~/types/form/subdetail'
  import { Lang } from '~/types/form/form-v1'

  defineComponent({
    name: 'TagsfieldDetail',
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
      type: Array,
      default: null,
    },
    uniqueId: {
      type: String,
      required: true,
    },
    parentEditor: {
      type: Object as () => FormSubDetail,
      required: true,
    },
  })

  const form = inject('form') as FormSubDetail
  const element = inject('element') as Element
  const tempVal: Ref<Array<string>> = ref(
    computed({
      get: () =>
        ObjectReader(tempValParent.value[props.rowIndex], props.field) || '',
      set: (val) =>
        ObjectUpdater(tempValParent.value[props.rowIndex], props.field, val),
    }),
  )
  const tempValParent = inject('tempValParent') as Ref<UnwrapRef<Array<any>>>

  const inputRef = ref()

  const isReady = ref(false)
  const tagify = ref()
  const nativeEdit = ref(false)

  const elementId = ref(
    `${props.uniqueId}-${props.field}-${props.rowIndex}`.replace('.', '-'),
  )

  const methods = {
    onChange: (evt) => {
      if (props.listener) {
        props.listener(element, evt.type, tempVal.value)
      }
    },
  }

  onMounted(() => {
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
          tempVal.value.length = 0
          JSON.parse(e.detail.value).forEach((x: any) => {
            tempVal.value.push(x.value)
          })
        } else {
          tempVal.value = []
        }
        methods.onChange({ type: 'change' })

        setTimeout(() => {
          nativeEdit.value = false
        }, 100)
      })

    // set default value
    if (tempVal.value && tempVal.value.length) {
      tempVal.value.forEach((dt) => {
        tagify.value.addTags(dt)
      })
    } else {
      props.default.forEach((dt) => {
        tagify.value.addTags(dt)
      })
    }

    setTimeout(() => {
      form.ready++
    }, 100)
  })

  watch(tempVal, () => {
    if (isReady.value && !nativeEdit.value) {
      tagify.value.removeAllTags()
      if (Array.isArray(tempVal.value)) {
        tempVal.value.forEach((item) => {
          tagify.value.addTags(item)
        })
      } else if (typeof tempVal.value === 'string') {
        JSON.parse(tempVal.value).forEach((item) => {
          tagify.value.addTags(item)
        })
      }
    }
  })
</script>
