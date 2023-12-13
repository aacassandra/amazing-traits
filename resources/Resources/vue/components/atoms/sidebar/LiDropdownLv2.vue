<template>
  <li class="dropdown-lv2">
    <div @click="onToggle">
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
        <i :class="{ 'fad fa-angle-down': true, expand: item.active }"></i>
      </div>
    </div>
    <ul
      v-if="!currentSidebarHide || sidebarMouseOver"
      :class="{ 'lvl-3': true, expand: item.active }"
    >
      <SidebarGeneratorLv3
        v-for="(subitem, i) in item.items"
        :key="i"
        :id-lvl1="idLvl1"
        :id-lvl2="idLvl2"
        :id-lvl3="i"
        :item="subitem"
      />
    </ul>
  </li>
</template>
<script setup lang="ts">
  import { defineComponent, inject } from 'vue'
  import SidebarGeneratorLv3 from '~/components/molecules/sidebar/SidebarGeneratorLv3.vue'
  import {
    Sidebar,
    SidebarsTextLink,
    SidebarsDropdownLvl3,
  } from '~/types/components/organism/sidebar'
  import { useConfigStore } from '~/store/config'

  defineComponent({
    name: 'LiDropdownLv2',
  })

  const props = defineProps({
    item: {
      type: Object as () => SidebarsTextLink | SidebarsDropdownLvl3,
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
  })

  const configStore = useConfigStore()
  const currentSidebarHide = inject('currentSidebarHide')
  const sidebarMouseOver = inject('sidebarMouseOver')
  const sidebar = inject('sidebar') as Sidebar
  const onToggle = () => {
    sidebar.data.forEach((dt, di) => {
      if (dt.type === 'dropdown' && di === props.idLvl1) {
        dt.items.forEach((dt2, di2) => {
          if (dt2.type === 'dropdown') {
            if (di2 === props.idLvl2) {
              dt2.active = !dt2.active
            } else {
              dt2.active = false
            }
          }
        })
      }
    })
  }
</script>
