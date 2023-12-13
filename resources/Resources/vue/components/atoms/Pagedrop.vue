<template>
  <div
    v-if="init || pagedropUI.enabled"
    :style="{
      position: 'fixed',
      width: '100%',
      height: '100%',
      backgroundColor: 'rgba(255, 255, 255, 0.8)',
      left: 0,
      top: 0,
      zIndex: 999,
    }"
    :class="{
      'transition-all duration-300 lg:duration-400': true,
      'fixed z-20 opacity-0': true,
      'opacity-100': init || pagedropUI.show,
    }"
  >
    <div
      style="width: 100%; height: 100%"
      class="z-20 text-6xl flex items-center justify-center bg-base-200 opacity-80"
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
  import {
    computed,
    defineComponent,
    inject,
    reactive,
    Ref,
    ref,
    UnwrapRef,
    watch,
  } from 'vue'
  import { useConfigStore } from '~/store/config'

  defineComponent({
    name: 'Pagedrop',
  })

  const init = inject('initPageDrop') as Ref<UnwrapRef<boolean>>
  const configStore = useConfigStore()
  const pagedrop = ref(computed(() => configStore.pagedrop))
  const configs = configStore
  const pagedropUI = reactive({
    enabled: false,
    show: false,
  })

  if (pagedrop.value.enabled) {
    pagedropUI.enabled = true
    setTimeout(() => {
      pagedropUI.show = true
      configs.disabled.scroll = true
    }, 10)
  }

  watch(
    pagedrop,
    () => {
      if (!pagedrop.value.enabled) {
        init.value = false
        setTimeout(() => {
          pagedropUI.show = false
          setTimeout(() => {
            pagedropUI.enabled = false
            configs.disabled.scroll = false
          }, pagedrop.value.fadeDuration)
        }, 200)
      } else {
        pagedropUI.enabled = true
        setTimeout(() => {
          pagedropUI.show = true
          configs.disabled.scroll = true
        }, 10)
      }
    },
    {
      deep: true,
    },
  )
</script>
