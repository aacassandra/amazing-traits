<template>
  <NavMainProfileItem v-if="item.type === 'profile'" :item="item" />
  <NavMainTextLinkItem v-else-if="item.type === 'text-link'" :item="item" />
  <NavMainSignOutItem v-else-if="item.type === 'sign-out'" :item="item" />
</template>

<script setup lang="ts">
  import { defineComponent, defineProps } from 'vue'
  import {
    NavMainProfileItem,
    NavMainTextLinkItem,
    NavMainSignOutItem,
  } from '~/components/atoms/header/desktop/nav'
  import {
    NavMainProfile,
    NavMainSignOut,
    NavMainTextLink,
  } from '~/types/config/navbars'

  defineComponent({
    name: 'NavMainItemGenerator',
  })

  defineProps({
    item: {
      type: Object as () => NavMainProfile | NavMainTextLink | NavMainSignOut,
      required: true,
    },
  })
</script>
