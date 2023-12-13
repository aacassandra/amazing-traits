<template>
  <div>
    <button
      type="button"
      class="btn btn-sm btn-accent"
      style="text-transform: unset"
      @click="methods.onShowModal"
    >
      Sub Detail
    </button>
    <form-validation
      :rows="tempValParent"
      :row-index="rowIndex"
      :field="field"
      :parent-editor="parentEditor"
    />
  </div>

  <Modal
    v-model="showModal"
    width="w-11/12 max-w-5xl"
    :keep-active="keepModalShow"
    :on-closed="methods.onClosedModal"
    :click-outside-disabled="modalLock"
  >
    <template #modal-title>
      {{ t(editor.options.title) }}
    </template>
    <template #modal-body>
      <SubDetailGenerator
        ref="formSubDetailRefs"
        :on-change="methods.onChange"
        :disabled="editor.options.disabled || isDisabled || disabled"
      />
    </template>
    <template #modal-footer>
      <div class="flex justify-end pt-6 pr-2">
        <button
          type="button"
          class="btn btn-sm btn-primary flex items-center text-white"
          :class="editor.options.verivyRow?.className"
          :disabled="editor.options.disabled || isDisabled || disabled"
          style="text-transform: unset"
          @click="methods.onVerivy"
        >
          <div>Confirm</div>
          <div class="ml-1">
            <i class="fas fa-check-circle"></i>
          </div>
        </button>
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
    provide,
    ref,
    Ref,
    UnwrapRef,
    watch,
  } from 'vue'
  import SubDetailGenerator from '../../../../organism/FormSubDetail/index.vue'
  import FormValidation from '../Validation.vue'
  import { Element } from '~/types/components/atoms/forms/detail'
  import { ClearArray, Notyf, ObjectReader, ObjectUpdater, t } from '~/services'
  import Modal from '~/components/atoms/Modal.vue'
  // import { SubDetailValidation } from '~/services/Validation'
  import { FormSubDetail } from '~/types/form/subdetail'
  import { Lang } from '~/types/form/form-v1'

  defineComponent({
    name: 'FormSubDetail',
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
    editor: {
      type: Object as () => FormSubDetail,
      required: true,
    },
    disabled: {
      // force disabled when parent subdetail want to disable all
      type: Boolean,
      default: false,
    },
    parentEditor: {
      type: Object as () => FormSubDetail,
      required: true,
    },
  })

  const form = inject('form') as FormSubDetail
  const formSubDetailRefs = ref()
  const element = inject('element') as Element

  const tempValParent = inject('tempValParent') as Ref<UnwrapRef<Array<any>>>
  const tempVal = ref(
    computed({
      get: () => ObjectReader(tempValParent.value[props.rowIndex], props.field),
      set: (val) =>
        ObjectUpdater(tempValParent.value[props.rowIndex], props.field, val),
    }),
  )
  const isDisabled = ref(
    computed({
      get: () =>
        ObjectReader(
          tempValParent.value[props.rowIndex],
          `disabled.${props.field}`,
        ),
      set: (val) =>
        ObjectUpdater(
          tempValParent.value[props.rowIndex],
          `disabled.${props.field}`,
          val,
        ),
    }),
  )

  // rows from detail
  const rows = inject('rows') as Ref<UnwrapRef<Array<any>>>
  const backupRows = ref(JSON.stringify(tempVal.value || []))
  const modalLock = ref(false)

  // rows for subdetail
  provide('rows', tempVal.value || [])
  provide('properties', props.editor.properties)
  provide('form', props.editor)
  provide('tempValParent', tempVal)
  provide('modalLock', modalLock)
  const showModal = ref(false)
  const keepModalShow = ref(false)
  provide('keepModalShow', keepModalShow)
  provide('showModal', showModal)
  const nativeEdit = ref(false)

  const methods = {
    onChange: (evt) => {
      nativeEdit.value = true
      if (evt.type === 'change') {
        ObjectUpdater(rows.value[props.rowIndex], props.field, tempVal.value)
      }

      if (props.editor.type === 'subdetailfield' && props.editor.listener) {
        props.editor.listener(
          element,
          evt.type,
          ObjectReader(rows.value[props.rowIndex], props.field) || [],
        )
      }

      setTimeout(() => {
        nativeEdit.value = false
      }, 100)
    },
    onShowModal: () => {
      if (props.editor.focusing) {
        const allow = props.editor.focusing(element)
        if (!allow) {
          return false
        }
      }

      showModal.value = true
      methods.onChange({ type: 'focus' })
    },
    onClosedModal: () => {
      const currentRows = JSON.stringify(tempVal.value)
      if (backupRows.value !== currentRows) {
        const backup = JSON.parse(backupRows.value)
        ClearArray(tempVal.value)
        backup.forEach((row) => {
          tempVal.value.push(row)
        })
        methods.onChange({ type: 'blur' })
      }
    },
    onVerivy: () => {
      if (showModal.value) {
        const errors = formSubDetailRefs.value.getValidRows()
        if (!errors) {
          if (props.editor.verivy) {
            const valid = props.editor.verivy(element, true)
            if (!valid) {
              return false
            }
          }

          showModal.value = false
          backupRows.value = JSON.stringify(tempVal.value)
          methods.onChange({ type: 'change' })
        } else {
          Notyf({
            type: 'error',
            message: 'Please correct your subdetail data',
            duration: 3000,
            ripple: true,
            dismissible: true,
            position: {
              x: 'right',
              y: 'top',
            },
          })

          if (props.editor.verivy) {
            props.editor.verivy(element, false)
          }
        }
      }
    },
  }

  onMounted(() => {
    form.ready++
  })

  watch(tempVal, () => {
    if (tempVal.value && !nativeEdit.value) {
      backupRows.value = JSON.stringify(tempVal.value || [])
      methods.onChange({ type: 'change' })
    }
  })
</script>
