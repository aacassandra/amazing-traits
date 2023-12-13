<template>
  <router-link :to="item.route" class="item">
    <i :class="item.icon"></i>
    <span>{{ item.text }}</span>
  </router-link>
</template>

<script setup lang="ts">
  import { defineComponent, defineProps } from 'vue'
  import { NavMainTextLink } from '~/types/config/navbars'

  defineComponent({
    name: 'NavMainTextLinkItem',
  })

  defineProps({
    item: {
      type: Object as () => NavMainTextLink,
      required: true,
    },
  })
</script>
