<template>
  <div
    class="min-h-screen max-h-screen text-gray-800"
    :style="{
      width: clientWidth < 1024 ? `${clientWidth}px` : 'auto',
      zIndex: clientWidth < 1024 ? 21 : 15,
      transform:
        clientWidth < 1024
          ? sidebar.hidden
            ? `translateX(-${clientWidth}px)`
            : 'translateX(0)'
          : 'translateX(0)',
    }"
  >
    <div
      :class="{
        'flex flex-col h-full transition-all duration-300 lg:duration-400': true,
        'w-64':
          (sidebar.hidden === false || sidebar.vHidden === false) &&
          clientWidth > 1023,
        'w-12':
          sidebar.hidden === true &&
          sidebar.vHidden === true &&
          clientWidth > 1023,
      }"
      @mouseenter="methods.onMouseEnter"
      @mouseleave="methods.onMouseLeave"
    >
      <div
        :class="{
          'w-full h-full border-r border-gray-200 dark:border-gray-600 side_bar': true,
          [configStore.getStyle.sidebar.bg.light]: true,
          [`dark:${configStore.getStyle.sidebar.bg.dark}`]:
            configStore.getStyle.sidebar.bg.dark,
          'overflow-y-auto': sidebarMouseOver,
        }"
      >
        <div class="flex items-center justify-center" style="height: 65px">
          <div
            :class="{
              'flex-grow-0 lg:flex-grow hidden lg:flex justify-center text-gray-600 dark:text-gray-200 ft-dongle-700 text-2xl': true,
              'md:pl-0': currentSidebarHide,
              'pl-5 lg:pl-0': !currentSidebarHide,
            }"
          >
            <AppLogo
              :full="!currentSidebarHide || sidebarMouseOver"
              mode="sidebar-cms"
            />
          </div>
          <div
            class="flex-grow-0 flex lg:hidden justify-start text-gray-600 dark:text-gray-200 ft-dongle-700 text-2xl pl-5"
          >
            Nutaboard
          </div>
          <div class="flex-grow flex justify-end lg:hidden pr-5">
            <div
              class="text-gray-600 dark:text-gray-200 text-xl cursor-pointer"
              @click="rootMethods.onToggleSidebar"
            >
              <i class="fas fa-times"></i>
            </div>
          </div>
        </div>
        <div
          class="overflow-y-auto overflow-x-hidden flex-grow"
          :style="{ height: `${clientHeight - 65}px` }"
        >
          <ul class="lvl-1">
            <SidebarGenerator
              v-for="(item, i) in sidebar.data"
              :id="i"
              :key="i"
              :item="item"
            />
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup lang="ts">
  import { inject, provide, ref, watch } from 'vue'
  import { Ref, UnwrapRef } from 'vue'
  import SidebarGenerator from '~/components/molecules/sidebar/SidebarGenerator.vue'
  import '~/assets/css/style.css'
  import { Sidebar } from '~/types/components/organism/sidebar'
  import AppLogo from '~/components/atoms/AppLogo.vue'
  import { useConfigStore } from '~/store/config'
  import { Notyf } from '~/services'

  type ClientWidth = {
    value: number
  }

  type ClientHeight = {
    value: number
  }

  const configStore = useConfigStore()
  const clientWidth = inject('clientWidth') as ClientWidth
  const clientHeight = inject('clientHeight') as ClientHeight
  const sidebar = inject('sidebar') as Sidebar
  const currentSidebarHide = ref<boolean>(false)
  const sidebarMouseOver = ref<boolean>(false)
  const progressToggle = ref<boolean>(false)
  const rootMethods = inject('rootMethods')
  provide('currentSidebarHide', currentSidebarHide)
  provide('sidebarMouseOver', sidebarMouseOver)

  const methods = {
    onMouseEnter: () => {
      if (clientWidth.value > 1023 && !progressToggle.value && sidebar.hidden) {
        sidebar.vHidden = false
        setTimeout(() => {
          sidebarMouseOver.value = true
        }, 200)
      }
    },
    onMouseLeave: () => {
      if (clientWidth.value > 1023 && !progressToggle.value && sidebar.hidden) {
        sidebar.vHidden = true
        sidebarMouseOver.value = false
      }
    },
    onToggle: () => {
      //
    },
    isMaintenance: () => {
      Notyf({
        type: 'info',
        message: 'Sorry this menu is under maintenance',
        duration: 3000,
        ripple: true,
        dismissible: true,
        position: {
          x: 'right',
          y: 'top',
        },
      })
    },
  }
  provide('parentMethods', methods)

  watch(sidebar, () => {
    if (!progressToggle.value) {
      progressToggle.value = true
      if (sidebar.hidden) {
        currentSidebarHide.value = true
      } else {
        setTimeout(() => {
          currentSidebarHide.value = false
        }, 200)
      }
      setTimeout(() => {
        progressToggle.value = false
      }, 500)
    }
  })

  const isMobile = inject('isMobile') as Ref<UnwrapRef<boolean>>
  watch(clientWidth, () => {
    if (clientWidth.value > 1023) {
      if (isMobile.value) {
        currentSidebarHide.value = false
      }
    } else if (!isMobile.value) {
      currentSidebarHide.value = false
    }
  })
</script>
<style>
  ul.lvl-1 {
    @apply flex flex-col py-4 space-y-1;
  }

  ul.lvl-1 > li.groupname {
    @apply px-5;
  }

  ul.lvl-1 > li.text-link > a,
  ul.lvl-1 > li.dropdown > div {
    @apply cursor-pointer flex flex-row items-center h-11 hover:bg-gray-50 dark:hover:bg-gray-600 text-gray-600 hover:text-gray-800 border-l-4 border-transparent hover:border-indigo-500;
  }

  ul.lvl-1 > li.text-link > a > .icon,
  ul.lvl-1 > li.dropdown > div > .icon {
    @apply inline-flex justify-center items-center w-12;
  }

  ul.lvl-1 > li.text-link > a > .text,
  ul.lvl-1 > li.dropdown > div > .text {
    font-family: Inter, sans-serif;
    font-weight: 500;
    @apply text-sm;
  }

  ul.lvl-1 > li.dropdown > div > .text > i {
    position: absolute;
    right: 20px;
    transform: translateY(50%) rotate(0);
    @apply duration-200;
  }

  ul.lvl-1 > li.dropdown > div > .text > i.expand {
    transform: translateY(25%) rotate(-180deg);
  }

  ul.lvl-2 {
    @apply hidden;
  }

  ul.lvl-2.expand {
    @apply block;
  }

  ul.lvl-2 > li.text-link > a,
  ul.lvl-2 > li.dropdown-lv2 > div {
    @apply cursor-pointer pl-4 flex flex-row items-center h-11 hover:bg-gray-50 dark:hover:bg-gray-600 text-gray-600 hover:text-gray-800 border-l-4 border-transparent hover:border-indigo-500;
  }

  ul.lvl-2 > li.text-link > a > .icon,
  ul.lvl-2 > li.dropdown-lv2 > div > .icon {
    @apply inline-flex justify-center items-center w-12;
  }

  ul.lvl-2 > li.text-link > a > .text,
  ul.lvl-2 > li.dropdown-lv2 > div > .text {
    font-family: Inter, sans-serif;
    font-weight: 500;
    @apply text-sm;
  }

  ul.lvl-2 > li.dropdown-lv2 > div > .text > i {
    position: absolute;
    right: 20px;
    transform: translateY(50%) rotate(0);
    @apply duration-200;
  }

  ul.lvl-2 > li.dropdown-lv2 > div > .text > i.expand {
    transform: translateY(25%) rotate(-180deg);
  }

  ul.lvl-3 {
    @apply hidden;
  }

  ul.lvl-3.expand {
    @apply block;
  }

  ul.lvl-3 > li.text-link > a,
  ul.lvl-3 > li.dropdown-lv3 > div {
    @apply cursor-pointer pl-8 flex flex-row items-center h-11 hover:bg-gray-50 dark:hover:bg-gray-600 text-gray-600 hover:text-gray-800 border-l-4 border-transparent hover:border-indigo-500;
  }

  ul.lvl-3 > li.text-link > a > .icon,
  ul.lvl-3 > li.dropdown-lv3 > div > .icon {
    @apply inline-flex justify-center items-center w-12;
  }

  ul.lvl-3 > li.text-link > a > .text,
  ul.lvl-3 > li.dropdown-lv3 > div > .text {
    font-family: Inter, sans-serif;
    font-weight: 500;
    @apply text-sm;
  }

  ul.lvl-3 > li.dropdown-lv3 > div > .text > i {
    position: absolute;
    right: 20px;
    transform: translateY(50%) rotate(0);
    @apply duration-200;
  }

  ul.lvl-3 > li.dropdown-lv3 > div > .text > i.expand {
    transform: translateY(25%) rotate(-180deg);
  }

  ul.lvl-4 {
    @apply hidden;
  }

  ul.lvl-4.expand {
    @apply block;
  }

  ul.lvl-4 > li.text-link > a,
  ul.lvl-4 > li.dropdown-lv2 > div {
    @apply cursor-pointer pl-12 flex flex-row items-center h-11 hover:bg-gray-50 dark:hover:bg-gray-600 text-gray-600 hover:text-gray-800 border-l-4 border-transparent hover:border-indigo-500;
  }

  ul.lvl-4 > li.text-link > a > .icon,
  ul.lvl-4 > li.dropdown-lv2 > div > .icon {
    @apply inline-flex justify-center items-center w-12;
  }

  ul.lvl-4 > li.text-link > a > .text,
  ul.lvl-4 > li.dropdown-lv2 > div > .text {
    font-family: Inter, sans-serif;
    font-weight: 500;
    @apply text-sm;
  }

  ul.lvl-4 > li.dropdown-lv2 > div > .text > i {
    position: absolute;
    right: 20px;
    transform: translateY(50%) rotate(0);
    @apply duration-200;
  }

  ul.lvl-4 > li.dropdown-lv2 > div > .text > i.expand {
    transform: translateY(25%) rotate(-180deg);
  }
</style>
