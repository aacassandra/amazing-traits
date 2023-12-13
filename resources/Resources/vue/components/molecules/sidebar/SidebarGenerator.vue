<template>
  <li
    v-if="
      item.type === 'groupname' &&
      CheckPermissions(item.permissions, permissions)
    "
    :class="item.type"
  >
    <div class="flex flex-row items-center h-8">
      <div
        :class="{
          'text-xs ft-inter-600 uppercase': true,
          [configStore.getStyle.sidebar.text.light]: true,
          [`dark:${configStore.getStyle.sidebar.text.dark}`]:
            configStore.getStyle.sidebar.text.dark,
          'opacity-0': currentSidebarHide && !sidebarMouseOver,
        }"
      >
        {{ item.lang ? $t(item.lang) : item.text }}
      </div>
    </div>
  </li>
  <li
    v-else-if="
      item.type === 'text-link' &&
      !item.hide &&
      CheckPermissions(item.permissions, permissions)
    "
    :class="item.type"
  >
    <router-link
      v-if="item.maintenance"
      to="#"
      @click="parentMethods.isMaintenance"
    >
      <span
        :class="{
          icon: true,
          [configStore.getStyle.sidebar.text.light]: true,
          [`dark:${configStore.getStyle.sidebar.text.dark}`]:
            configStore.getStyle.sidebar.text.dark,
        }"
        v-html="item.icon"
      ></span>
      <div
        v-if="!currentSidebarHide || sidebarMouseOver"
        :class="{
          text: true,
          [configStore.getStyle.sidebar.text.light]: true,
          [`dark:${configStore.getStyle.sidebar.text.dark}`]:
            configStore.getStyle.sidebar.text.dark,
        }"
      >
        {{ item.lang ? $t(item.lang) : item.text }}
      </div>
    </router-link>
    <router-link v-else :to="item.route" @click="hideSibebarWhenClickAtMobile">
      <span
        :class="{
          icon: true,
          [configStore.getStyle.sidebar.text.light]: true,
          [`dark:${configStore.getStyle.sidebar.text.dark}`]:
            configStore.getStyle.sidebar.text.dark,
        }"
        v-html="item.icon"
      ></span>
      <div
        v-if="!currentSidebarHide || sidebarMouseOver"
        :class="{
          text: true,
          [configStore.getStyle.sidebar.text.light]: true,
          [`dark:${configStore.getStyle.sidebar.text.dark}`]:
            configStore.getStyle.sidebar.text.dark,
        }"
      >
        {{ item.lang ? $t(item.lang) : item.text }}
      </div>
    </router-link>
  </li>
  <LiDropdown
    v-else-if="
      item.type === 'dropdown' &&
      !item.hide &&
      CheckPermissions(item.permissions, permissions)
    "
    :id="id"
    :item="item"
  />
</template>

<script setup lang="ts">
  import { defineComponent, inject, Ref, UnwrapRef, defineProps } from 'vue'
  import {
    SidebarsDropdown,
    SidebarsGroup,
    SidebarsTextLink,
  } from '~/types/components/organism/sidebar'
  import LiDropdown from '~/components/atoms/sidebar/LiDropdown.vue'
  import { MethodsLayoutCMS } from '~/types/layouts/cms'
  import { CheckPermissions } from '~/services'
  import { useConfigStore } from '~/store/config'

  defineComponent({
    name: 'SidebarGenerator',
  })

  defineProps({
    item: {
      type: Object as () => SidebarsGroup | SidebarsTextLink | SidebarsDropdown,
      required: true,
    },
    id: {
      type: Number,
      required: true,
    },
  })

  const configStore = useConfigStore()
  const permissions = inject('permissions') as Ref<UnwrapRef<Array<any>>>
  const clientWidth = inject('clientWidth') as Ref<UnwrapRef<number>>
  const currentSidebarHide = inject('currentSidebarHide')
  const sidebarMouseOver = inject('sidebarMouseOver')
  const rootMethods = inject('rootMethods') as MethodsLayoutCMS
  const parentMethods = inject('parentMethods')

  const hideSibebarWhenClickAtMobile = () => {
    if (clientWidth.value < 1024) {
      rootMethods.onToggleSidebar()
    }
  }
</script>
