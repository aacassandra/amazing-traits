<template>
  <div
    v-if="clientWidth"
    class="transition-all duration-300"
    :style="{
      zIndex: 30,
      position: 'fixed',
      width: headerWidth(),
    }"
  >
    <div
      style="width: 100%"
      :class="{
        'navbar mb-2 shadow-md': true,
        [`${configStore.getStyle.header.bg.light}`]: true,
        [`dark:${configStore.getStyle.header.bg.dark}`]:
          configStore.getStyle.header.bg.dark,
      }"
    >
      <div class="flex-none flex mr-2">
        <div
          :class="{
            'text-xl cursor-pointer px-3': true,
            [`${configStore.getStyle.header.text.light}`]: true,
            [`dark:${configStore.getStyle.header.text.dark}`]:
              configStore.getStyle.header.text.dark,
          }"
          @click="rootMethods.onToggleSidebar"
        >
          <i class="far fa-bars"></i>
        </div>
      </div>
      <div class="flex-1 hidden lg:flex lg:flex-none">
        <div
          style="height: 40px; width: 300px"
          :class="{
            'bg-gray-100 dark:bg-gray-800 rounded-md py-3 px-0 pl-3 flex items-center hidden': true,
            'ring-2 ring-gray-300 ring-opacity-50': inFocusSearch,
          }"
        >
          <input
            type="text"
            placeholder="Search"
            class="flex-grow input-search bg-gray-100 dark:bg-gray-800 text-black border-0 focus:outline-none"
            @focus="onFocusSearch"
            @blur="onFocusSearch"
          />
          <div
            class="flex-grow-0 text-gray-600 dark:text-gray-300 px-3 text-base"
          >
            <i class="fas fa-search"></i>
          </div>
        </div>
      </div>
      <div class="flex-1 justify-end button-theme">
        <div
          v-if="localization === 'true'"
          class="dropdown dropdown-hover dropdown-end mr-4"
        >
          <div
            v-if="lang === 'en'"
            class="flex items-center space-x-[8px] cursor-pointer"
          >
            <img
              class="w-[16px] h-[16x]"
              src="/image/flags/en-flag.svg"
              alt="Flag Icon"
            />
            <span class="font-semibold">EN</span>
          </div>
          <div
            v-if="lang === 'id'"
            class="flex items-center space-x-[8px] cursor-pointer"
          >
            <img
              class="w-[16px] h-[16x]"
              src="/image/flags/id-flag.svg"
              alt="Flag Icon"
            />
            <span class="font-semibold">ID</span>
          </div>
          <ul
            tabindex="0"
            class="dropdown-content menu p-2 shadow bg-base-100 rounded-box w-52"
          >
            <li @click="rootMethods.onToggleLanguage('en')">
              <a :class="{ 'font-bold': lang === 'en' }"> English </a>
            </li>
            <li @click="rootMethods.onToggleLanguage('id')">
              <a :class="{ 'font-bold': lang === 'id' }"> Bahasa </a>
            </li>
          </ul>
        </div>
        <div class="dropdown dropdown-hover dropdown-end">
          <label
            tabindex="0"
            class="btn btn-sm btn-ghost text-lg text-gray-600 dark:text-gray-300"
          >
            <i
              v-if="theme === 'light'"
              class="fa-solid icon-theme-light fa-sun-bright"
            ></i>
            <i
              v-if="theme === 'dark'"
              class="fa-solid icon-theme-dark fa-moon-stars"
            ></i>
          </label>
          <ul
            tabindex="0"
            class="dropdown-content menu p-2 bg-base-100 dark:bg-gray-700 shadow-lg rounded-box w-52"
          >
            <li>
              <a
                :class="{ 'font-bold': theme === 'light' }"
                @click="rootMethods.onToggleTheme('light')"
              >
                <i class="fa-solid fa-sun-bright"></i> Light
              </a>
            </li>
            <li>
              <a
                :class="{ 'font-bold': theme === 'dark' }"
                @click="rootMethods.onToggleTheme('dark')"
              >
                <i class="fa-solid fa-moon-stars"></i> Dark
              </a>
            </li>
          </ul>
        </div>
        <div
          v-if="authStore.user && authStore.user.roles.length > 1"
          class="dropdown dropdown-hover dropdown-end"
        >
          <label
            tabindex="0"
            class="btn btn-sm btn-ghost text-lg text-gray-600 dark:text-gray-300"
          >
            <i class="fa-solid fa-network-wired"></i>
          </label>
          <ul
            tabindex="0"
            class="dropdown-content menu p-2 bg-base-100 dark:bg-gray-700 shadow-lg rounded-box w-52"
          >
            <li v-for="(role, index) in authStore.user.roles" :key="index">
              <a @click="rootMethods.onChangeRolePersonaly(role)">
                <b v-if="role === authStore.user.role">{{ role }}</b>
                <span v-else>{{ role }}</span>
              </a>
            </li>
          </ul>
        </div>
      </div>
      <div class="flex-none pr-3" style="margin-top: -8px">
        <nav class="header-menu">
          <ul>
            <NavGenerator
              v-for="(item, i) in navbarItems"
              :key="i"
              :item="item"
            />
          </ul>
        </nav>
      </div>
    </div>
  </div>
</template>
<script setup lang="ts">
  import { defineProps, inject, ref, Ref, UnwrapRef } from 'vue'
  import { NavMain, NavNotification } from '~/types/config/navbars'
  import NavGenerator from '~/components/organism/header/desktop/NavGenerator.vue'
  import { useConfigStore } from '~/store/config'
  import { useAuthStore } from '~/store/auth'

  defineProps({
    navbarItems: {
      type: Array as () => Array<NavMain | NavNotification>,
      required: true,
    },
  })

  const theme = inject('theme')
  const lang = inject('lang')
  const localization = import.meta.env.VITE_LOCALIZATION
  const inFocusSearch = ref(false)
  const configStore = useConfigStore()
  const authStore = useAuthStore()
  const rootMethods = inject('rootMethods')
  const clientWidth = inject('clientWidth') as Ref<UnwrapRef<number>>
  const sidebar = inject('sidebar') as {
    hidden: boolean
    vHidden: boolean
    data: any
  }
  const onFocusSearch = () => {
    inFocusSearch.value = !inFocusSearch.value
  }
  const headerWidth = () => {
    return clientWidth.value > 1023
      ? sidebar.hidden
        ? sidebar.vHidden
          ? `${clientWidth.value - 48}px`
          : `${clientWidth.value - 256}px`
        : `${clientWidth.value - 256}px`
      : `${clientWidth.value}px`
  }
</script>
<style scoped>
  .header-menu ul {
    padding: 5px;
    @apply block;
  }

  .header-menu ul :deep(li) {
    list-style: none;
    @apply inline-block bg-gray-200 dark:bg-gray-600 hover:bg-gray-300 dark:hover:bg-gray-800 rounded-md cursor-pointer ml-2;
  }

  .header-menu ul :deep(li) .show {
    list-style: none;
    @apply ring ring-gray-300 dark:ring-gray-500 ring-opacity-50;
  }

  :deep(li.nav-dropdown) {
    transform: translateY(4px);
    @apply flex flex-col flex-col-reverse py-1.5 px-2;
  }

  :deep(li.nav-link) {
    transform: translateY(-4px);
    @apply px-2.5;
  }

  :deep(li.nav-dropdown.nav-dropdown-notification) {
    transform: translateY(-4px);
    @apply px-2.5;
  }

  .header-menu ul :deep(li > .menu-head) {
    font-family: 'DM Sans', sans-serif;
    font-weight: 500;
    font-size: 15px;
    @apply flex items-center no-underline transition duration-500 text-gray-700 dark:text-gray-200;
  }

  .header-menu ul :deep(li .menu-dropdown) {
    margin-top: 40px;
    @apply absolute rounded-md flex flex-wrap bg-base-100 dark:bg-base-300 shadow-md invisible opacity-0 transition-all duration-200 border border-solid border-gray-100 dark:border-gray-600;
  }

  :deep(li.nav-dropdown.nav-dropdown-main > .menu-dropdown) {
    width: 200px;
    transform: translate(-80px, 0);
  }

  :deep(li.nav-dropdown.nav-dropdown-notification > .menu-dropdown) {
    width: 300px;
    transform: translate(-260px, 0);
  }

  :deep(li.nav-dropdown.nav-dropdown-main.show > .menu-dropdown) {
    transform: translate(-80px, -5px);
    @apply visible opacity-100;
  }

  :deep(li.nav-dropdown.nav-dropdown-notification.show > .menu-dropdown) {
    transform: translate(-260px, -5px);
    @apply visible opacity-100;
  }

  .header-menu ul :deep(li .menu-dropdown > .item) {
    @apply w-full transition duration-500 no-underline py-4 flex items-center bg-base-100;
  }

  .header-menu ul :deep(li .menu-dropdown > .item > i) {
    font-size: 14px;
    @apply text-gray-500 dark:text-gray-200 px-4 m-0;
  }

  .header-menu ul :deep(li .menu-dropdown > .item > span) {
    font-size: 14px;
    font-family: 'Nunito700', sans-serif;
    @apply text-gray-400 dark:text-gray-300 m-0;
  }

  .header-menu ul :deep(li .menu-dropdown > .item:hover > i),
  .header-menu ul :deep(li .menu-dropdown > .item:hover > span) {
    @apply text-gray-600 dark:text-white;
  }

  .header-menu ul :deep(li .menu-dropdown > .item.item-sign-out) {
    @apply border-t border-gray-100 dark:border-gray-600;
  }

  .header-menu ul :deep(li .menu-dropdown > .item.item-sign-out:hover > i),
  .header-menu ul :deep(li .menu-dropdown > .item.item-sign-out:hover > span) {
    @apply text-red-500;
  }

  .header-menu ul :deep(li .menu-dropdown > .item.item-profile) {
    @apply flex flex-col items-center justify-center cursor-default rounded-t-md bg-gray-100 dark:bg-gray-600;
  }

  .header-menu ul :deep(li .menu-dropdown > .item.item-label) {
    font-family: 'Nunito700', sans-serif;
    font-size: 15px;
    @apply flex items-center justify-center cursor-default rounded-t-md bg-gray-100 dark:bg-gray-600 text-gray-600 dark:text-gray-200 uppercase;
  }

  .header-menu ul :deep(li .menu-dropdown > .item.item-notif) {
    @apply flex items-start justify-center border-t border-gray-200 dark:border-gray-500 hover:bg-gray-100 dark:hover:bg-gray-600 transition-none;
  }

  .header-menu ul :deep(li .menu-dropdown > .item.item-load-more) {
    @apply flex items-center justify-center rounded-b-md bg-gray-100 dark:bg-gray-600 py-2 border-t border-gray-200 dark:border-gray-500;
  }

  :deep(.item-notif-icon) {
    min-width: 45px;
    @apply flex-grow-0 flex justify-center pt-1;
  }

  :deep(.item-notif-rightside) {
    @apply flex-grow flex flex-col justify-center;
  }

  :deep(.item-notif-rightside > .notif-title) {
    font-family: 'Nunito700', sans-serif;
    font-size: 15px;
    @apply leading-5;
  }

  :deep(.item-notif-rightside > .notif-createdat) {
    font-family: 'Nunito400', sans-serif;
    font-size: 13px;
    @apply text-gray-400 leading-3;
  }

  .header-menu ul :deep(li .menu-dropdown > .item.item-profile > .name) {
    font-family: 'Nunito700', sans-serif;
    @apply text-gray-500 dark:text-gray-200 text-xl mt-6 leading-3;
  }

  .header-menu ul :deep(li .menu-dropdown > .item.item-profile > .email) {
    font-family: 'Nunito600', sans-serif;
    @apply text-gray-400 text-base;
  }

  .header-menu ul :deep(li > .menu-head.notification) {
    height: 24px;
    @apply flex;
  }

  .header-menu ul :deep(li > .menu-head.notification .notification-dot) {
    width: 5px;
    height: 5px;
    border-radius: 50%;
    @apply bg-blue-500 ml-2;
  }

  .header-menu ul :deep(li > .menu-head > .menu-text) {
    @apply text-black dark:text-gray-200 mx-2;
  }

  .header-menu ul :deep(li > .menu-head > i.fa-chevron-down) {
    @apply transform transition duration-500 text-gray-700 dark:text-gray-200 rotate-0 pt-1;
  }

  .header-menu ul :deep(li.show > .menu-head > i.fa-chevron-down) {
    @apply rotate-180;
  }

  .header-menu
    ul
    :deep(li > .menu-dropdown:hover ~ .menu-head > i.fa-chevron-down) {
    @apply rotate-180;
  }
</style>
