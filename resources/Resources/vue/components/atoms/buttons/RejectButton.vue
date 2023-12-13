<template>
  <button
    type="button"
    class="btn btn-error dark:text-white btn-sm"
    style="text-transform: none"
    @click="methods.onClick"
  >
    <slot /> <i class="fas fa-exclamation-circle ml-1"></i>
  </button>
</template>

<script setup lang="ts">
  import { defineComponent, defineProps } from 'vue'

  defineComponent({
    name: 'RejectButton',
  })

  const props = defineProps({
    onClick: {
      type: Function,
      required: true,
    },
  })

  const methods = {
    onClick: () => props.onClick(),
  }
</script>
