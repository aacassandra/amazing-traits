<template>
  <li
    v-click-outside="onClickOutside"
    :class="{ 'nav-dropdown nav-dropdown-main': true, show: show }"
  >
    <div class="menu-dropdown">
      <NavMainItemGenerator
        v-for="(subitem, i) in item.items"
        :key="i"
        :item="subitem"
      />
    </div>
    <div class="menu-head" @click="onToggle">
      <div class="avatar">
        <div class="rounded-full w-6 h-6">
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
      <div class="menu-text">
        {{ authStore.user ? `${authStore.user.name}` : '' }}
      </div>
      <i class="fas fa-chevron-down ft-sz-13"></i>
    </div>
  </li>
</template>

<script setup lang="ts">
  import { defineComponent, ref, defineProps } from 'vue'
  import { NavMain } from '~/types/config/navbars'
  import NavMainItemGenerator from '~/components/molecules/header/desktop/NavMainItemGenerator.vue'
  import { useAuthStore } from '~/store/auth'

  defineComponent({
    name: 'NavMainItem',
  })

  defineProps({
    item: {
      type: Object as () => NavMain,
      required: true,
    },
  })

  const authStore = useAuthStore()
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
