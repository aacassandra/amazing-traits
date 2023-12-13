<template>
  <div
    class="flex-grow-0 w-auto h-100 text-black flex items-center justify-center"
  >
    <div
      v-if="full"
      class="ml-3 flex flex-col justify-center"
      style="height: 60px"
    >
      <img
        v-if="theme === 'light'"
        src="/image/logo/logo-full.png"
        alt="ihome"
        :style="{ height: mode === 'sidebar-cms' ? '150px' : '150px' }"
      />
      <img
        v-else
        src="/image/logo/logo-full-wh.png"
        alt="ihome"
        :style="{ height: mode === 'sidebar-cms' ? '150px' : '150px' }"
      />
    </div>
    <div v-else>
      <img
        v-if="theme === 'light'"
        src="/image/logo/logo.png"
        alt="amr-aware-logo"
        :style="{
          width: full
            ? mode === 'sidebar-cms'
              ? clientWidth > 991
                ? '40px'
                : '35px'
              : '55px'
            : mode === 'sidebar-cms'
            ? '40px'
            : '60px',
        }"
      />
      <img
        v-else
        src="/image/logo/logo-wh.png"
        alt="amr-aware-logo"
        :style="{
          width: full
            ? mode === 'sidebar-cms'
              ? clientWidth > 991
                ? '40px'
                : '35px'
              : '55px'
            : mode === 'sidebar-cms'
            ? '40px'
            : '60px',
        }"
      />
    </div>
  </div>
</template>

<script setup lang="ts">
  import { defineComponent, inject } from 'vue'

  defineComponent({
    name: 'AppLogo',
  })

  defineProps({
    full: {
      type: Boolean,
      default: false,
    },
    mode: {
      type: String as () => 'sign-in' | 'sidebar-cms',
      required: true,
    },
  })

  const clientWidth = inject('clientWidth')
  const theme = inject('theme')
</script>
<style scoped>
  .app-logo-dark {
    filter: grayscale(51%) sepia(100%) brightness(150%) hue-rotate(153deg)
      saturate(132%) contrast(448%);
    -webkit-filter: grayscale(51%) sepia(100%) brightness(150%)
      hue-rotate(153deg) saturate(132%) contrast(448%);
    -moz-filter: grayscale(51%) sepia(100%) brightness(150%) hue-rotate(153deg)
      saturate(132%) contrast(448%);
  }
  .app-logo-light {
    filter: grayscale(100%) brightness(400%) contrast(122%);
    -webkit-filter: grayscale(100%) brightness(400%) contrast(122%);
    -moz-filter: grayscale(100%) brightness(400%) contrast(122%);
  }
</style>
