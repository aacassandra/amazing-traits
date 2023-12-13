<template>
  <div
    v-if="backdropUI.enabled"
    :style="{
      position: 'fixed',
      top: `70px`,
      right: 0,
      width: sidebar.hidden ? '100%' : 'calc(100% - 16rem)',
      height: `${clientHeight - 70}px`,
      zIndex: 999,
    }"
    :class="{
      'absolute z-20 transition-all duration-300 lg:duration-400 opacity-0': true,
      'opacity-100': backdropUI.show,
    }"
  >
    <div
      style="width: 100%; height: 100%"
      class="z-20 text-6xl flex items-center justify-center bg-base-100 opacity-90"
    ></div>
    <div
      style="width: 100%; height: 100%; position: absolute; top: 0"
      class="z-30 text-6xl flex items-center justify-center text-gray-400 dark:text-gray-600"
    >
      <i class="fad fa-spinner-third fa-spin"></i>
    </div>
  </div>
</template>

<script setup lang="ts">
  import { computed, inject, reactive, watch } from 'vue'
  import { useConfigStore } from '~/store/config'

  const configs = useConfigStore()
  const backdrop = computed(() => configs.backdrop)
  const backdropUI = reactive({
    enabled: false,
    show: false,
  })
  const sidebar = inject('sidebar')
  const clientHeight = inject('clientHeight')

  watch(
    backdrop,
    () => {
      if (!backdrop.value.enabled) {
        backdropUI.show = false
        setTimeout(() => {
          backdropUI.enabled = false
          // configs.disabled.scroll = false
        }, backdrop.value.fadeDuration)
      } else {
        backdropUI.enabled = true
        setTimeout(() => {
          backdropUI.show = true
          // configs.disabled.scroll = true
        }, 10)
      }
    },
    {
      deep: true,
    },
  )
</script>
