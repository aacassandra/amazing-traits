<template>
  <div v-if="modelValue">
    <div
      :style="{
        position: 'fixed',
        top: 0,
        left: 0,
        width: `${clientWidth}px`,
        height: `${clientHeight}px`,
        'background-color': firstly
          ? modalHasActive
            ? 'rgba(0, 0, 0, 0.4)'
            : 'rgba(0, 0, 0, 0)'
          : 'rgba(0, 0, 0, 0)',
        zIndex: 350 + currentCounter,
      }"
      :class="{
        'transition-all duration-400 flex justify-center': true,
        'opacity-0': !backdropShow,
        'opacity-100': backdropShow,
        'items-center': position === 'center',
        'items-start pt-5': position === 'top',
        'items-end': position === 'bottom',
      }"
    >
      <div
        v-click-outside="methods.onOusideClick"
        :class="{
          [`${width} bg-base-100 rounded-md shadow-xl`]: true,
          'p-6': !noPadding,
          'p-0': noPadding,
          'opacity-0': !modalHasActive,
          'opacity-100': modalHasActive,
        }"
      >
        <div class="flex items-center justify-between p-6">
          <h3 class="text-2xl">
            <slot name="modal-title"></slot>
          </h3>
          <label
            class="btn btn-sm btn-circle bg-gray-700 dark:bg-white text-white dark:text-gray-700"
            @click="methods.onCloseModal"
          >
            ✕
          </label>
        </div>
        <div v-if="modalBodyBeforeShow" class="mt-4">
          <slot name="modal-header"></slot>
          <slot name="modal-body"></slot>
          <slot name="modal-footer"></slot>
        </div>
        <div
          v-else
          :class="{ 'mt-4 flex justify-center': true, 'pb-6': noPadding }"
        >
          <i class="fad fa-spinner-third fa-spin"></i>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup lang="ts">
  import { inject, ref, watch, defineProps, defineEmits } from 'vue'
  import { Ref, UnwrapRef } from 'vue'
  import { useCounterStore } from '~/store/counter'
  import { useTempsStore } from '~/store/temps'

  const props = defineProps({
    modelValue: {
      type: Boolean,
      default: false,
    },
    width: {
      type: String,
      default: 'w-11/12 max-w-full',
    },
    keepActive: {
      type: Boolean,
      default: false,
    },
    onClosing: {
      type: Function,
      default: null,
    },
    onClosed: {
      type: Function,
      default: null,
    },
    clickOutsideDisabled: {
      type: Boolean,
      default: false,
    },
    position: {
      type: String as () => 'center' | 'bottom' | 'top',
      default: 'center',
    },
    noPadding: {
      type: Boolean,
      default: false,
    },
  })

  const emit = defineEmits(['update:modelValue'])
  const modalHasActive = ref(false)
  const clientWidth = inject('clientWidth') as Ref<UnwrapRef<number>>
  const clientHeight = inject('clientHeight') as Ref<UnwrapRef<number>>
  const counter = useCounterStore()
  const currentCounter = ref(0)
  const backdropShow = ref(false)
  const firstly = ref(false)
  const nativeClose = ref(false)
  const modalBodyBeforeShow = ref(false)
  const tempsStore = useTempsStore()
  const methods = {
    onOusideClick: () => {
      if (!props.clickOutsideDisabled && !tempsStore.ignoreClickOutsideModal) {
        methods.onCloseModal()
      }
    },
    onCloseModal: () => {
      if (
        currentCounter.value === counter.getValue('modal') &&
        !props.keepActive
      ) {
        nativeClose.value = true
        if (props.onClosing) {
          props.onClosing()
        }
        backdropShow.value = false
        counter.decrement()
        setTimeout(() => {
          modalHasActive.value = false
          emit('update:modelValue', false)
          if (props.onClosed) {
            props.onClosed()
          }
          setTimeout(() => {
            nativeClose.value = false
            modalBodyBeforeShow.value = false
          }, 100)
        }, 400)
      }
    },
  }

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
      if (
        !nativeClose.value &&
        props.modelValue &&
        !tempsStore.ignoreClickOutsideModal
      ) {
        methods.onCloseModal()
      }
    }
  })

  watch(
    () => props.modelValue,
    () => {
      if (!nativeClose.value) {
        if (props.modelValue) {
          counter.increment()
          currentCounter.value = counter.getValue('modal')

          firstly.value = counter.modal <= 1

          setTimeout(() => {
            backdropShow.value = true
            setTimeout(() => {
              modalHasActive.value = true
              setTimeout(() => {
                modalBodyBeforeShow.value = true
              }, 500)
            }, 100)
          }, 10)
        } else {
          counter.decrement()
          currentCounter.value = counter.getValue('modal')

          firstly.value = counter.modal <= 1

          setTimeout(() => {
            backdropShow.value = false
            setTimeout(() => {
              modalHasActive.value = false
            }, 100)
          }, 10)
        }
      }
    },
  )
</script>
