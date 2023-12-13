<template>
  <div class="item item-profile">
    <div class="avatar">
      <div class="rounded-full w-14 h-14">
        <img
          alt=""
          :src="
            authStore.user
              ? `https://avatar.oxro.io/avatar.svg?name=${authStore.user.name}&background=73bf94&color=000&length=1`
              : `https://avatar.oxro.io/avatar.svg?name=John+Smith&background=73bf94&color=000`
          "
        />
      </div>
    </div>
    <div class="name">
      {{ authStore.user ? authStore.user.username : '' }}
    </div>
    <div class="email ft-sz-12">
      {{ authStore.user ? authStore.user.email : '' }}
    </div>
  </div>
</template>

<script setup lang="ts">
  import { defineComponent } from 'vue'
  import { NavMainProfile } from '~/types/config/navbars'
  import { useAuthStore } from '~/store/auth'

  defineComponent({
    name: 'NavMainProfileItem',
  })

  defineProps({
    item: {
      type: Object as () => NavMainProfile,
      required: true,
    },
  })

  const authStore = useAuthStore()
</script>
