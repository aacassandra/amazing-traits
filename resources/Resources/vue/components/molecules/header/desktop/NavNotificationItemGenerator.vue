<template>
  <NavNotificationLabelItem v-if="item.type === 'label'" :item="item" />
  <NavNotificationItem v-else-if="item.type === 'item'" :item="item" />
  <NavNotificationLoadMoreItem
    v-else-if="item.type === 'load-more'"
    :item="item"
  />
</template>

<script setup lang="ts">
  import { defineComponent } from 'vue'
  import {
    NavNotificationsLabel,
    NavNotificationsLoadMore,
    NavNotificationsItem,
  } from '~/types/config/navbars'
  import {
    NavNotificationLabelItem,
    NavNotificationItem,
    NavNotificationLoadMoreItem,
  } from '~/components/atoms/header/desktop/nav'

  defineComponent({
    name: 'NavNotificationItemGenerator',
  })

  defineProps({
    item: {
      type: Object as () =>
        | NavNotificationsLabel
        | NavNotificationsItem
        | NavNotificationsLoadMore,
      required: true,
    },
  })
</script>
