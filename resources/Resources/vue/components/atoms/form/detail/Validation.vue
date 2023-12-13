<template>
  <div
    v-if="rows[rowIndex].errors && rows[rowIndex].errors(form, rowIndex, field)"
    class="text-red-600 dark:text-red-500 text-xs mt-1"
  >
    {{ rows[rowIndex].errors(form, rowIndex, field) }}
    <i class="fas fa-exclamation-circle"></i>
  </div>
</template>

<script setup lang="ts">
import { FormDetail } from '~/types/form/detail'

defineProps({
  rows: {
    type: Array,
    required: true
  },
  rowIndex: {
    type: Number,
    required: true
  },
  field: {
    type: String,
    required: true
  },
  form: {
    type: Object as () => FormDetail,
    required: true
  }
})
</script>
