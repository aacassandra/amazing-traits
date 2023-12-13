<template>
  <DetailCore
    :is-ready="isReady"
    :is-loading="isLoading"
    :multi-key="multiKey"
  />
</template>
<script setup lang="ts">
  import { defineProps, inject, Ref, UnwrapRef } from 'vue'
  import DetailCore from './DetailCore.vue'

  defineProps({
    isLoading: {
      type: Boolean,
      default: false,
    },
    multiKey: {
      type: String,
      default: null,
    },
  })

  const isReady = inject('isReady') as Ref<UnwrapRef<boolean>>
</script>
