<template>
  <div class="grid grid-cols-12 mt-6 z-20 relative">
    <div class="col-span-12">loading development</div>
  </div>
</template>

<script setup lang="ts">
  /* eslint-disable no-console */
  import { defineProps, inject, onMounted, reactive } from 'vue'
  import { Element, OldParams } from '~/types/components/atoms/forms/header'

  const props = defineProps({
    isMultiple: {
      type: Boolean,
      default: false,
    },
    properties: {
      type: Object as () => Element,
      default: null,
    },
  })

  const clientWidth = inject('clientWidth')
  const temp = inject('tempVal') as {
    value_: any
    displayValue: string
    showModal: boolean
    value: any
  }

  // Helper for multiple select only
  const fix = reactive({
    displayValue: '',
    value: [],
  })
  let cacheControl: RequestCache = 'default'
  const theme = inject('theme')
  const nativeEdit = inject<{ value: boolean }>('nativeEdit')
  const methods = {
    //
  }

  onMounted(() => {
    //
  })
</script>
