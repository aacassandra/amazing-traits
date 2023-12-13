<template>
  <li class="dropdown-lv3">
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
      :class="{ 'lvl-4': true, expand: item.active }"
    >
      <SidebarGeneratorLv4
        v-for="(subitem, i) in item.items"
        :key="i"
        :item="subitem"
        :id-lvl1="idLvl1"
        :id-lvl2="idLvl2"
        :id-lvl3="idLvl3"
        :id-lvl4="i"
      />
    </ul>
  </li>
</template>
<script setup lang="ts">
  import { defineComponent, inject } from 'vue'
  import SidebarGeneratorLv4 from '~/components/molecules/sidebar/SidebarGeneratorLv4.vue'
  import {
    SidebarsTextLink,
    SidebarsDropdownLvl3,
    Sidebar,
  } from '~/types/components/organism/sidebar'
  import { useConfigStore } from '~/store/config'

  defineComponent({
    name: 'LiDropdownLv3',
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
    idLvl3: {
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
          if (dt2.type === 'dropdown' && di2 === props.idLvl2) {
            dt2.items.forEach((dt3, di3) => {
              if (dt3.type === 'dropdown') {
                if (di3 === props.idLvl3) {
                  dt3.active = !dt3.active
                } else {
                  dt3.active = false
                }
              }
            })
          }
        })
      }
    })
  }
</script>
