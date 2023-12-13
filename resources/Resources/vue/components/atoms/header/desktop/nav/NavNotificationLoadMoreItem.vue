<template>
  <router-link
    :to="item.route"
    class="item item-load-more text-blue-500 dark:text-yellow-300 hover:text-blue-700 dark:hover:text-yellow-500"
  >
    <div class="mr-2">
      <i class="fad fa-arrow-down"></i>
    </div>
    <div>
      {{ item.text }}
    </div>
  </router-link>
</template>

<script setup lang="ts">
  import { defineComponent } from 'vue'
  import { NavNotificationsLoadMore } from '~/types/config/navbars'

  defineComponent({
    name: 'NavNotificationLoadMoreItem',
  })

  defineProps({
    item: {
      type: Object as () => NavNotificationsLoadMore,
      required: true,
    },
  })
</script>
