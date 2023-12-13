<template>
  <div
    class="w-full mt-5 transition-all duration-200"
    :style="{
      width: getWidth(),
    }"
  >
    <div
      class="py-2 rounded-lg shadow-sm border border-gray-300 dark:border-gray-600 bg-base-100"
      style="width: inherit; height: auto"
    >
      <div
        v-if="$slots['card-header']"
        class="border-b border-gray-300 dark:border-gray-600 px-3 pb-2"
      >
        <slot name="card-header"></slot>
      </div>
      <div
        v-if="$slots['card-body']"
        :class="{
          'px-3': true,
          'py-4': $slots['card-footer'],
          'pt-3 pb-1': !$slots['card-footer'],
        }"
      >
        <slot name="card-body"></slot>
      </div>
      <div
        v-if="$slots['card-footer']"
        class="border-t border-gray-300 dark:border-gray-600 px-3 pt-2"
      >
        <slot name="card-footer"></slot>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
  import { defineComponent, inject, onMounted, ref } from 'vue'
  import { Sidebar } from '~/types/components/organism/sidebar'

  defineComponent({
    name: 'Card',
  })

  const sidebarSize = ref()
  const sidebar = inject('sidebar') as Sidebar
  const clientWidth = inject('clientWidth') as { value: number }

  const calculate = () => {
    if (clientWidth.value > 1023) {
      if (sidebar.hidden) {
        sidebarSize.value = 48
      } else {
        sidebarSize.value = 256
      }
    } else {
      sidebarSize.value = 0
    }
  }

  const getWidth = () => {
    return '100%'
    // if (clientWidth.value > 1400) {
    //   if (sidebar.hidden) {
    //     return `${clientWidth.value - 188}px`
    //   } else {
    //     return `${clientWidth.value - 256 - 150}px`
    //   }
    // } else if (clientWidth.value > 1023) {
    //   if (sidebar.hidden) {
    //     return `${clientWidth.value - 188}px`
    //   } else {
    //     return `${clientWidth.value - 256 - 50}px`
    //   }
    // } else if (clientWidth.value > 700) {
    //   return `${clientWidth.value - 130}px`
    // } else {
    //   return `${clientWidth.value - 47}px`
    // }
  }

  onMounted(() => {
    calculate()
  })
</script>
