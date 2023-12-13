<template>
  <button
    type="button"
    class="btn btn-success text-white dark:text-white btn-sm"
    style="text-transform: none"
    :disabled="disabled || isLoading"
    @click="methods.onClick"
  >
    <slot /> <i v-if="!isLoading" class="fas fa-check-circle ml-1"></i
    ><i v-else class="fad fa-spinner-third fa-spin ml-1"></i>
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
    disabled: {
      type: Boolean,
      default: false,
    },
    isLoading: {
      type: Boolean,
      default: false,
    },
  })

  const methods = {
    onClick: () => props.onClick(),
  }
</script>
