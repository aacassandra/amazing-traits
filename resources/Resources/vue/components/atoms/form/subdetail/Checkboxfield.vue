<template>
  <div>
    <div class="flex relative items-center">
      <input
        :id="elementId"
        v-model="tempVal_"
        type="checkbox"
        class="checkbox checkbox-primary"
        :disabled="isDisabled !== undefined ? isDisabled : disabled"
        :readonly="readonly || false"
        @change="methods.onChange({ type: 'change' })"
      />
    </div>
    <form-validation
      :rows="tempValParent"
      :row-index="rowIndex"
      :field="field"
      :parent-editor="parentEditor"
    />
  </div>
</template>

<script setup lang="ts">
  import {
    defineComponent,
    inject,
    onMounted,
    ref,
    watch,
    defineProps,
    reactive,
  } from 'vue'
  import { Ref, UnwrapRef } from 'vue'
  import FormValidation from './Validation.vue'
  import { Element } from '~/types/components/atoms/forms/detail'
  import { ObjectReader, ObjectUpdater, t } from '~/services'
  import { computed } from 'vue'
  import { FormSubDetail } from '~/types/form/subdetail'
  import { TextListenerConfig } from '~/types/components/atoms/forms/detail/text'
  import { Lang } from '~/types/form/form-v1'

  defineComponent({
    name: 'TextfieldDetail',
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
      type: [Boolean, Number],
      default: '',
    },
    uniqueId: {
      type: String,
      required: true,
    },
    parentEditor: {
      type: Object as () => FormSubDetail,
      required: true,
    },
    listenerConfig: {
      type: Object as () => TextListenerConfig,
      default: () => {
        return {
          exclude: [],
        }
      },
    },
  })

  const form = inject('form') as FormSubDetail
  const element = inject('element') as Element
  const tempValParent = inject('tempValParent') as Ref<UnwrapRef<Array<any>>>

  const isNumber = ref(typeof props.default === 'number')
  const tempVal_ = ref(props.default)
  const tempVal = reactive({
    value: computed({
      get: () =>
        ObjectReader(tempValParent.value[props.rowIndex], props.field) || '',
      set: (val) =>
        ObjectUpdater(tempValParent.value[props.rowIndex], props.field, val),
    }),
  })

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
  const nativeEdit = ref(false)

  const elementId = ref(
    `${props.uniqueId}-${props.field}-${props.rowIndex}`.replace('.', '-'),
  )

  const methods = {
    onChange: (evt) => {
      nativeEdit.value = true

      if (isNumber.value) {
        tempVal.value = tempVal_.value ? 1 : 0
      } else {
        tempVal.value = tempVal_.value
      }

      if (props.listener) {
        props.listener(element, evt.type, tempVal_.value)
      }

      setTimeout(() => {
        nativeEdit.value = false
      }, 100)
    },
  }

  const hasInit = ref(false)

  onMounted(() => {
    setTimeout(() => {
      if (tempVal.value !== undefined || true) {
        tempVal_.value = tempVal.value
      }
      hasInit.value = true
    }, 100)
    form.ready++
  })

  // only triggers itself
  watch(tempVal, () => {
    if (hasInit.value && tempVal_.value !== tempVal.value) {
      tempVal_.value = tempVal.value
    }
  })
</script>
