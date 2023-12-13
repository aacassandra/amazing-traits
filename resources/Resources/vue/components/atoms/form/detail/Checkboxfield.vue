<template>
  <div>
    <div class="absolute" style="margin-top: -17px">
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
      :rows="temp.form.rows"
      :row-index="rowIndex"
      :field="field"
      :form="temp.form"
    />
  </div>
</template>

<script setup lang="ts">
  import {
    defineComponent,
    inject,
    onMounted,
    ref,
    defineProps,
    watch,
    reactive,
  } from 'vue'
  import FormValidation from './Validation.vue'
  import { FormDetail } from '~/types/form/detail'
  import { Element } from '~/types/components/atoms/forms/detail'
  import { GetRandomString, ObjectReader, ObjectUpdater, t } from '~/services'
  import { computed } from 'vue'
  import { Lang } from '~/types/form/form-v1'

  defineComponent({
    name: 'CheckboxfieldDetail',
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
      default: null,
    },
  })

  const temp = inject('temp') as {
    form: FormDetail
  }
  const element = inject('element') as Element
  const isNumber = ref(typeof props.default === 'number')
  const tempVal_ = ref(props.default)
  const tempVal = reactive({
    value: computed({
      get: () => ObjectReader(temp.form.rows[props.rowIndex], props.field),
      set: (val) =>
        ObjectUpdater(temp.form.rows[props.rowIndex], props.field, val),
    }),
  })
  const isDisabled = ref(
    computed({
      get: () =>
        ObjectReader(temp.form.rows[props.rowIndex], `disabled.${props.field}`),
      set: (val) =>
        ObjectUpdater(
          temp.form.rows[props.rowIndex],
          `disabled.${props.field}`,
          val,
        ),
    }),
  )

  const uniqeId = GetRandomString(5)
  const elementId = ref(
    `${props.field}-${props.rowIndex}-${uniqeId}`.replace('.', '-'),
  )
  const nativeEdit = ref(false)
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
    temp.form.ready++
  })

  // temp is all rows when change
  watch(temp, () => {
    if (!hasInit.value) {
      hasInit.value = true
      if (tempVal.value === undefined) {
        // create new data
        tempVal.value = props.default
      } else {
        // triggerd when edit
        tempVal_.value = tempVal.value
      }

      methods.onChange({ type: 'init' })
    }
  })

  // only triggers itself
  watch(tempVal, () => {
    if (hasInit.value && tempVal_.value !== tempVal.value) {
      tempVal_.value = tempVal.value
    }
  })
</script>
