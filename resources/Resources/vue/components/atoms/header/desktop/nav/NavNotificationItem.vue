<template>
  <router-link :to="item.route" class="item item-notif">
    <div class="item-notif-icon">
      <i :class="`${item.icon} ${item.iconColor}`"></i>
    </div>
    <div class="item-notif-rightside">
      <div class="notif-title text-gray-600 dark:text-gray-200">
        {{ item.title }}
      </div>
      <div class="notif-createdat">
        {{ item.createdAt }}
      </div>
    </div>
  </router-link>
</template>

<script setup lang="ts">
  import { defineComponent } from 'vue'
  import { NavNotificationsItem } from '~/types/config/navbars'

  defineComponent({
    name: 'NavNotificationsItem',
  })

  defineProps({
    item: {
      type: Object as () => NavNotificationsItem,
      required: true,
    },
  })
</script>
