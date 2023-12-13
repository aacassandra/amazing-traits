<template>
  <li class="dropdown">
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
      :class="{ 'lvl-2': true, expand: item.active }"
    >
      <SidebarGeneratorLv2
        v-for="(subitem, i) in item.items"
        :key="i"
        :id-lvl1="id"
        :id-lvl2="i"
        :item="subitem"
      />
    </ul>
  </li>
</template>

<script setup lang="ts">
  import { defineComponent, inject } from 'vue'
  import SidebarGeneratorLv2 from '~/components/molecules/sidebar/SidebarGeneratorLv2.vue'
  import {
    Sidebar,
    SidebarsTextLink,
    SidebarsDropdownLvl2,
  } from '~/types/components/organism/sidebar'
  import { useConfigStore } from '~/store/config'

  defineComponent({
    name: 'LiDropdown',
  })

  const props = defineProps({
    item: {
      type: Object as () => SidebarsTextLink | SidebarsDropdownLvl2,
      required: true,
    },
    id: {
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
      if (dt.type === 'dropdown') {
        if (di === props.id) {
          dt.active = !dt.active
        } else {
          dt.active = false
        }

        dt.items.forEach((dt2) => {
          if (dt2.type === 'dropdown') {
            dt2.active = false
            dt2.items.forEach((dt3) => {
              if (dt3.type === 'dropdown') {
                dt3.active = false
              }
            })
          }
        })
      }
    })
  }
</script>
