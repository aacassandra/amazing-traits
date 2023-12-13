<template>
  <div class="item item-label">
    {{ item.text }}
  </div>
</template>

<script setup lang="ts">
import { defineComponent } from 'vue'
import { NavNotificationsLabel } from '~/types/config/navbars'

defineComponent({
  name: 'NavNotificationsLabelItem'
})

defineProps({
  item: {
    type: Object as () => NavNotificationsLabel,
    required: true
  }
})
</script>
