import { defineStore } from 'pinia'
import { ObjectReader, ObjectUpdater } from '~/services'
import { useConfigStore } from '~/store/config'

export interface User {
  id: number
  permissions: Array<string>
  role: string
  roles: Array<string>
  createdAt: Date
  updatedAt: Date
  username: string
  email: string
  name: string
}

export interface RootState {
  user?: User
  loggedIn: boolean
}

export interface InitAuthStore extends RootState {
  setValue(path: string, value: any): void
  getValue(path: string): any
}

export const useAuthStore = defineStore({
  id: 'auth-store',

  /**
   * All state can be set in here
   */
  state: (): RootState => {
    return {
      user: undefined,
      loggedIn: false,
    }
  },

  /**
   * called when using dispatch
   * the dispatch function is useful for executing a function you want to run
   *
   * example:
   * const store = useStore()
   * store.dispatch('getUsers')
   *
   */
  actions: {
    save(user: any) {
      return new Promise((resolve) => {
        const configStore = useConfigStore()
        configStore.pagedrop.enabled = true
        setTimeout(() => {
          localStorage.setItem('_loggedIn', 'true')
          localStorage.setItem('_token', `Bearer ${user.token}`)
          localStorage.setItem('_me', JSON.stringify(user))
          ObjectUpdater(this, 'user', user)
          ObjectUpdater(this, 'loggedIn', true)

          resolve(true)
          setTimeout(() => {
            configStore.pagedrop.enabled = false
          }, 1500)
        }, 1000)
      })
    },
    saveMe(user: any) {
      return new Promise((resolve) => {
        localStorage.setItem('_me', JSON.stringify(user))
        ObjectUpdater(this, 'loggedIn', true)
        ObjectUpdater(this, 'user', user)
        resolve(true)
      })
    },
  },

  /**
   * getters is a function to set state from vuex state to component state
   * like binding value 2 ways communication
   *
   * example:
   * computed: {
   *   ...mapGetters({
   *     users: 'getUsers'
   *   })
   * }
   *
   */
  getters: {
    /**
     * Get state with path location
     * @param theStatez
     */
    getUser: (theState: RootState) => {
      return () => ObjectReader(theState, 'user')
    },
    getLoggedIn: (theState: RootState) => {
      return () => {
        const loggedIn = localStorage.getItem('_loggedIn')
        if (loggedIn) {
          theState.loggedIn = true
          return true
        } else {
          return false
        }
      }
    },
    getMe: (theState: RootState) => {
      return () => {
        return JSON.parse(localStorage.getItem('_me'))
      }
    },
  },
})
