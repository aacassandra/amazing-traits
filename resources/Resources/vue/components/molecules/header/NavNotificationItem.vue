<template>
  <li
    v-click-outside="onClickOutside"
    :class="{ 'nav-dropdown nav-dropdown-notification': true, show: show }"
  >
    <div class="menu-dropdown">
      <NavNotificationItemGenerator
        v-for="(subitem, i) in item.items"
        :key="i"
        :item="subitem"
      />
    </div>
    <div class="menu-head notification" @click="onToggle">
      <i :class="item.icon"></i>
      <div v-if="item.availableNew" class="notification-dot"></div>
    </div>
  </li>
</template>
<script setup lang="ts">
import { defineComponent, ref } from 'vue'
import { NavNotification } from '~/types/config/navbars'
import NavNotificationItemGenerator from '~/components/molecules/header/desktop/NavNotificationItemGenerator.vue'

defineComponent({
  name: 'NavNotificationItem'
})

defineProps({
  item: {
    type: Object as () => NavNotification,
    required: true
  }
})

const show = ref(false)
const onToggle = () => {
  show.value = !show.value
}

const onClickOutside = () => {
  if (show.value) {
    show.value = false
  }
}
</script>
