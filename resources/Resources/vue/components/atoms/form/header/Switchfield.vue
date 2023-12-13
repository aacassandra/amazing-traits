<template>
  <div class="grid grid-cols-12">
    <div :class="{ 'col-span-6': docs, 'col-span-12': !docs }">
      <div :class="{ flex: true, 'flex-column': !inline }">
        <div
          :style="{
            minWidth: inline ? (clientWidth < 640 ? 'unset' : '150px') : '',
            maxWidth: inline ? (clientWidth < 640 ? 'unset' : '150px') : '',
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
            'relative flex-grow pt-2': inline || clientWidth < 640,
            'flex-grow pt-2': !inline || clientWidth < 640,
          }"
        >
          <div class="flex relative items-center">
            <label :for="name" class="cursor-pointer">
              <input
                :id="name"
                v-model="temp.value_"
                type="checkbox"
                class="sr-only"
                :disabled="disabled || false"
                @change="methods.onChange({ type: 'change' })"
              />
              <div
                class="w-11 h-6 bg-gray-200 rounded-full border border-gray-200 toggle-bg dark:bg-gray-700 dark:border-gray-600"
              ></div>
            </label>
          </div>
        </div>
      </div>
    </div>
    <div v-if="docs" :class="{ 'col-span-6 pl-3': true, 'pt-7': !inline }">
      <Highlight language="js" :code="code" />
    </div>
  </div>
</template>

<script setup lang="ts">
  import { defineComponent, inject, onMounted, ref, watch } from 'vue'
  import { computed, reactive, defineProps, defineEmits } from 'vue'
  import { Highlight } from '~/components/atoms'
  import { Notyf, t } from '~/services'
  import { Lang } from '~/types/form/form-v1'

  defineComponent({
    name: 'Switchfield',
  })

  const props = defineProps({
    name: {
      type: String,
      required: true,
    },
    modelValue: {
      type: [Boolean, Number],
      default: null,
    },
    outVal: {
      type: [Boolean, Number],
      default: null,
    },
    label: {
      type: String,
      default: 'Label',
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
      type: [Boolean, Number],
      default: null,
    },
  })

  const clientWidth = inject('clientWidth')
  const emit = defineEmits(['update:modelValue', 'update:ready'])
  const isNumber = ref(typeof props.default === 'number')
  const temp = reactive({
    value_: !!props.default,
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

  const methods = {
    onChange: (evt) => {
      nativeEdit.value = true

      if (isNumber.value) {
        temp.value = temp.value_ ? 1 : 0
      } else {
        temp.value = temp.value_
      }

      if (props.listener) {
        props.listener(props.properties, evt.type, temp.value_)
      }

      setTimeout(() => {
        nativeEdit.value = false
      }, 100)
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
    // set default value
    if (props.default !== null) {
      temp.value = props.default
    }
    if (props.fromGenerator) {
      setTimeout(() => {
        temp.ready++
      }, 100)
    }
  })

  watch(
    () => props.outVal,
    () => {
      if (!nativeEdit.value && temp.value !== props.outVal) {
        temp.value_ = !!props.outVal
      }
    },
  )

  const code = ref(`
  {
    type: 'switchfield',
    value: false,
    col: 'col-span-12 lg:col-span-6',
    options: {
      label: 'Switch',
      information: 'Help text example: Switch',
      inline: true,
      disabled: false,
      hidden: false
    },
    listener: (element, type, value) => {
      //
    },
  }
`)
</script>
