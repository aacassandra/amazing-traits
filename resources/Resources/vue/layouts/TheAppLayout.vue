<script setup lang="ts">
  // path: src/layouts/TheAppLayout.vue

  import { computed, defineComponent } from 'vue'
  import { useRoute } from 'vue-router'

  defineComponent({
    name: 'TheAppLayout',
  })

  const route = useRoute()
  /**
   *  This is a computed property that will return the name
   *  of the current route
   */
  const layout = computed(() => {
    const layout = route?.meta?.layout

    if (layout) {
      return `The${layout}Layout`
    }
    return 'div'
  })
</script>

<template>
  <component :is="layout">
    <router-view />
  </component>
</template>
