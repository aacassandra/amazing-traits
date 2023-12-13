<template>
  <button
    type="button"
    class="btn btn-sm btn-error btn-outline normal-case flex items-center"
    @click="methods.onClick"
  >
    <div class="mr-1">
      <i class="fal fa-arrow-from-right"></i>
    </div>
    <slot />
  </button>
</template>

<script setup lang="ts">
  import { defineComponent, defineProps } from 'vue'
  import router from '~/router'

  defineComponent({
    name: 'BackButton',
  })

  const props = defineProps({
    route: {
      type: String,
      required: true,
    },
    params: {
      type: Object,
      default: () => null,
    },
  })

  const methods = {
    onClick: () => {
      router.push({ name: props.route, query: props.params })
    },
  }
</script>
