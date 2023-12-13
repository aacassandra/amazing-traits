<template>
  <div class="row">
    <div
      v-if="properties.errors && properties.errors(name)"
      :class="{
        'my-auto text-red-600 dark:text-red-500 col-auto': true,
        'mt-2': !noMarginTop,
        'mt-0': noMarginTop
      }"
      style="line-height: 19px"
    >
      <span v-if="properties.errors" class="text-xs">
        {{ properties.errors(name) }}
        <i class="fas fa-exclamation-circle"></i>
      </span>
    </div>
    <div
      :class="{
        'flex my-auto text-xs text-gray-500 dark:text-gray-300': true,
        'col-12':
          !properties.errors || (properties.errors && !properties.errors(name)),
        'col justify-end': properties.errors && properties.errors(name)
      }"
    >
      <slot name="help-text"></slot>
    </div>
  </div>
</template>

<script setup lang="ts">
import { defineComponent } from 'vue'

defineComponent({
  name: 'FormValidation'
})

defineProps({
  name: {
    type: String,
    required: true
  },
  noMarginTop: {
    type: Boolean,
    default: false
  },
  properties: {
    type: Object,
    default: null
  }
})
</script>
