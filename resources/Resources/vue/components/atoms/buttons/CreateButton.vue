<template>
  <button
    type="button"
    class="btn btn-sm btn-outline normal-case flex items-center text-primary hover:bg-primary hover:border-primary dark:hover:text-white"
    @click="methods.onClick"
  >
    <slot />
    <div class="ml-1">
      <i class="fal fa-layer-plus"></i>
    </div>
  </button>
</template>

<script setup lang="ts">
  import { defineComponent, defineProps } from 'vue'
  import router from '~/router'

  defineComponent({
    name: 'CreateButton',
  })

  const props = defineProps({
    route: {
      type: String,
      required: true,
    },
  })

  const methods = {
    onClick: () => {
      router.push({ name: props.route })
    },
  }
</script>
