<template>
  <li
    v-if="
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
  <LiDropdownLv3
    v-else-if="
      item.type === 'dropdown' &&
      !item.hide &&
      CheckPermissions(item.permissions, permissions)
    "
    :item="item"
    :id-lvl1="idLvl1"
    :id-lvl2="idLvl2"
    :id-lvl3="idLvl3"
  />
</template>

<script setup lang="ts">
  import { defineComponent, inject, Ref, UnwrapRef } from 'vue'
  import {
    SidebarsTextLink,
    SidebarsDropdownLvl2,
  } from '~/types/components/organism/sidebar'
  import LiDropdownLv3 from '~/components/atoms/sidebar/LiDropdownLv3.vue'
  import { MethodsLayoutCMS } from '~/types/layouts/cms'
  import { CheckPermissions } from '~/services'
  import { useConfigStore } from '~/store/config'

  defineComponent({
    name: 'SidebarGeneratorLv3',
  })

  defineProps({
    item: {
      type: Object as () => SidebarsTextLink | SidebarsDropdownLvl2,
      required: true,
    },
    idLvl1: {
      type: Number,
      required: true,
    },
    idLvl2: {
      type: Number,
      required: true,
    },
    idLvl3: {
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
