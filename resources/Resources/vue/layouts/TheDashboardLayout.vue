<template>
  <Pagedrop />
  <div class="flex bg-base-100">
    <Sidebar
      :class="{
        'z-10 transition-transform duration-200 lg:duration-400': true,
        'flex-grow-0': clientWidth > 1023,
        'fixed h-full': clientWidth < 1024,
      }"
    />
    <div
      class="flex-grow flex flex-col z-20"
      :style="{ height: `${clientHeight}px`, width: '100%', overflow: 'auto' }"
    >
      <div>
        <div
          :style="{
            width: '100%',
            height: `${clientHeight}px`,
          }"
          :class="{
            'z-10 relative overflow-y-auto overflow-x-hidden': true,
            'overflow-y-hidden': configs.disabled.scroll,
          }"
        >
          <Header :navbar-items="navbars" />
          <Backdrop />
          <div class="mx-auto px-10 py-5" style="margin-top: 70px">
            <slot></slot>
          </div>
        </div>
      </div>
    </div>
  </div>
  <TailwindColors />
</template>
<script setup lang="ts">
  import { defineComponent, inject } from 'vue'
  import { Backdrop, Pagedrop, TailwindColors } from '~/components/atoms'
  import { Sidebar, Header } from '~/components/organism'
  import { useConfigStore } from '~/store/config'
  import { navbars, sidebars } from '~/config'

  defineComponent({
    name: 'TheDashboardLayout',
  })

  const clientWidth = inject('clientWidth')
  const clientHeight = inject('clientHeight')
  const configs = useConfigStore()
</script>
