<template>
  <div
    v-if="
      rows[rowIndex].errors &&
      rows[rowIndex].errors(rows, parentEditor, rowIndex, field)
    "
    class="text-red-600 dark:text-red-500 text-xs mt-1"
  >
    {{ rows[rowIndex].errors(rows, parentEditor, rowIndex, field) }}
    <i class="fas fa-exclamation-circle"></i>
  </div>
</template>

<script setup lang="ts">
import { inject } from 'vue'
import { FormSubDetail } from '~/types/form/subdetail'

const props = defineProps({
  rows: {
    type: Array as () => Array<any>,
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
  parentEditor: {
    type: Object as () => FormSubDetail,
    required: true
  },
  debug: {
    type: Boolean,
    required: false
  }
})

const form = inject('form') as FormSubDetail
</script>
