<template>
  <TheAppLayout />
</template>
<script setup lang="ts">
  import { useAuthStore } from '~/store/auth'
  import { useConfigStore } from '~/store/config'
  import { computed, onMounted, provide, reactive, ref, watch } from 'vue'
  import { MethodsLayoutCMS } from '~/types/layouts/cms'
  import { Axios, Notyf, SyncronizeMenu, t } from '~/services'
  import i18n from './locales'
  import { useWebsocketStore } from '~/store/websocket'
  import { useOnline } from '@vueuse/core'
  import { useSidebarsStore } from '~/store/sidebar'
  import '~/assets/css/style.font.css'

  const online = useOnline()
  const table_prefix = import.meta.env.VITE_APP_TABLE_PREFIX
  const sidebarsStore = useSidebarsStore()
  const authStore = useAuthStore()
  const configStore = useConfigStore()
  const websocketStore = useWebsocketStore()
  const initPageDrop = ref(true)
  let themeLocal = localStorage.getItem('_theme')
  let langLocal = localStorage.getItem('_lang')
  const cookieLoggedIn = localStorage.getItem('_loggedIn')
  const loggedIn = ref(computed(() => authStore.loggedIn))
  const permissions = ref<Array<string>>([])
  const pageHasInit = ref(false)
  const stylesHasLoaded = ref(false)
  watch(
    () => authStore.user,
    () => {
      if (authStore.user?.permissions.length) {
        permissions.value = authStore.user?.permissions
      } else {
        permissions.value.pop()
      }

      if (!pageHasInit.value) {
        configStore.init().then(() => {
          if (authStore.loggedIn) {
            Axios({
              url: `/api/v1/${table_prefix}configs?where=params='styles'`,
              method: 'GET',
            }).then((res: any) => {
              const data = res.data.data[0]
              configStore.styleId = data.id
              configStore.save(JSON.parse(data.value))

              stylesHasLoaded.value = true
              setTimeout(() => {
                pageHasInit.value = true
              }, 500)
            })
          }
        })
      }
    },
  )

  if (cookieLoggedIn) {
    authStore.loggedIn = true
    SyncronizeMenu(sidebarsStore.getSidebars)
  }

  websocketStore.init()

  const theme = ref<string>('light')
  const lang = ref<string>('en')
  const title = ref<string>('-')
  const clientWidth = ref<number>(0)
  const clientHeight = ref<number>(0)
  const isMobile = ref(false)

  const scrollY = ref<number>()
  const sidebar = reactive({
    hidden: false,
    vHidden: true,
    data: computed(() => sidebarsStore.getSidebars),
  })
  const fromTable = ref(false)

  const el = $(`html[data-theme]`)
  const methods: MethodsLayoutCMS = {
    myEventHandler() {
      clientWidth.value = document.documentElement.clientWidth
      clientHeight.value = document.documentElement.clientHeight

      setTimeout(() => {
        isMobile.value = clientWidth.value < 1024
      }, 200)
    },
    handleScroll: () => {
      scrollY.value = window.scrollY
    },
    onToggleLanguage: (langTarget = '') => {
      lang.value = langTarget
      localStorage.setItem('_lang', langTarget)
      i18n.global.locale.value = langTarget
    },
    onToggleTheme: (mode = '') => {
      el.attr('data-theme', '')
      el.removeClass('light')
      el.removeClass('dark')
      el.removeClass('system')

      theme.value = mode
      localStorage.setItem('_theme', mode)
      el.addClass(mode)
      el.attr('data-theme', mode)
    },
    onToggleSidebar: () => {
      sidebar.hidden = !sidebar.hidden
    },
    onChangeRolePersonaly: (role_target) => {
      Axios({
        method: 'POST',
        url: `/api/v1/${table_prefix}m_users/change_role_personaly`,
        data: {
          role_target,
        },
      }).then((res: any) => {
        authStore.saveMe(res.data.data)
      })
    },
    getPreferredScheme: () => {
      return window?.matchMedia?.('(prefers-color-scheme:dark)')?.matches
        ? 'dark'
        : 'light'
    },
    setThemeFlatpickr: () => {
      $('link[href="/vendor/flatpickr/light.css"]').remove()
      $('link[href="/vendor/flatpickr/dark.css"]').remove()
      setTimeout(() => {
        let element = document.createElement('link')
        element.setAttribute('rel', 'stylesheet')
        element.setAttribute('type', 'text/css')
        element.setAttribute('href', `/vendor/flatpickr/${theme.value}.css`)
        element.setAttribute('id', 'flatpikr-script') // not work, removing using id
        document.getElementsByTagName('head')[0].appendChild(element)
      }, 300)
    },
  }

  onMounted(() => {
    document.body.scrollTop = 0
    document.documentElement.scrollTop = 0
    window.addEventListener('resize', methods.myEventHandler)
    window.addEventListener('scroll', () => {
      scrollY.value = window.scrollY
    })
    clientWidth.value = document.documentElement.clientWidth
    clientHeight.value = document.documentElement.clientHeight
    isMobile.value = clientWidth.value < 1024
    if (!themeLocal) {
      localStorage.setItem('_theme', 'light')
      theme.value = 'light'
    } else {
      theme.value = themeLocal
    }
    methods.setThemeFlatpickr()
    if (!langLocal) {
      localStorage.setItem('_lang', 'en')
      lang.value = 'en'
    } else {
      lang.value = langLocal
    }
    i18n.global.locale.value = lang.value
    el.addClass(theme.value)
    el.attr('data-theme', theme.value)
  })

  watch(clientWidth, () => {
    sidebar.hidden =
      typeof clientWidth.value === 'number' && clientWidth.value < 1024
  })
  watch(theme, () => {
    methods.setThemeFlatpickr()
  })

  provide('initPageDrop', initPageDrop)
  provide('theme', theme)
  provide('lang', lang)
  provide('clientWidth', clientWidth)
  provide('clientHeight', clientHeight)
  provide('scrollY', scrollY)
  provide('rootMethods', methods)
  provide('sidebar', sidebar)
  provide('fromTable', fromTable) // handling conditonal back button on crud page
  provide('isMobile', isMobile)
  provide('pageHasInit', pageHasInit)
  provide('stylesHasLoaded', stylesHasLoaded)
  provide('permissions', permissions)

  watch(online, () => {
    Notyf({
      type: online.value ? 'success' : 'error',
      message: online.value
        ? t('information.is_online')
        : t('information.is_offline'),
      duration: 3000,
      ripple: true,
      dismissible: true,
      position: {
        x: 'right',
        y: 'top',
      },
    })
  })
</script>
<style>
  @import 'assets/css/custom.switch.css';
  @import 'assets/css/custom.select2.css';
  @import 'assets/css/custom.tagify-dtl.css';
  @import 'assets/css/custom.select2.detail.css';
  @import 'assets/css/custom.selectize.css';
  @import 'assets/css/custom.summernote-lite.css';

  .progress {
    @apply bg-gray-100 dark:bg-gray-500;
  }
  .progress::after {
    @apply bg-gray-700 dark:bg-gray-200;
  }
</style>
