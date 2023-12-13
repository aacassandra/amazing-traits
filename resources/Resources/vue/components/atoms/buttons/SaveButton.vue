<template>
  <button
    type="button"
    class="btn btn-success dark:text-white btn-sm"
    style="text-transform: none"
    @click="methods.onClick"
  >
    <slot /> <i class="fas fa-save ml-1"></i>
  </button>
</template>

<script setup lang="ts">
  import { defineComponent, defineProps } from 'vue'

  defineComponent({
    name: 'SubmitButton',
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
