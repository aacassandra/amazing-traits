<template>
  <NavMainItem v-if="item.type === 'main'" :item="item" />
  <!--  <NavNotificationItem v-else-if="item.type === 'notification'" :item="item" />-->
</template>

<script setup lang="ts">
  import { defineComponent, defineProps } from 'vue'
  import {
    NavMainItem,
    // NavNotificationItem
  } from '~/components/molecules/header'
  import { NavNotification, NavMain } from '~/types/config/navbars'

  defineComponent({
    name: 'NavGenerator',
  })

  defineProps({
    item: {
      type: Object as () => NavMain | NavNotification,
      required: true,
    },
  })
</script>
