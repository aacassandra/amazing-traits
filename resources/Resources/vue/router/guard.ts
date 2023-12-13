import { NavigationGuard, Router } from 'vue-router'
import { useAuthStore } from '~/store/auth'
import { Auth } from '~/services'
import { useConfigStore } from '~/store/config'
import { useSidebarsStore } from '~/store/sidebar'

const authGuard: NavigationGuard = async (to, from, next) => {
  const authStore = useAuthStore()
  const configStore = useConfigStore()
  const sidebarsStore = useSidebarsStore()
  try {
    await Auth.me()
    await sidebarsStore.getWorktaskMenu()
    if (authStore.loggedIn) {
      setTimeout(() => {
        configStore.pagedrop.enabled = false
      }, 1500)
      next()
    } else {
      next('/')
    }
  } catch (e) {
    next('/')
  }
}

export default function (router: Router) {
  router.beforeEach((to, from, next) => {
    const store = useAuthStore()
    if (to.meta.requiresAuth) {
      if (store.loggedIn) {
        authGuard(to, from, next)
      } else {
        next('/')
      }
    } else {
      next()
    }
  })
}
