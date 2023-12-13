import { defineStore } from 'pinia'
import { ObjectReader, ObjectUpdater } from '~/services'
import Echo from 'laravel-echo'
import Pusher from 'pusher-js'

export interface RootState {
  pusher: any
  echo: any
}

// echo.value
//   .channel('public_channel')
//   .listen('.ihm_t_app_reports_d_chats', (e) => {
//     // your logic here
//     // support listen when data is created, updated or deleted
//   })

// echo.value
//   .private('private_channel')
//   .listen('.ihm_t_app_reports_d_chats', (e) => {
//     // your logic here
//     // support listen when data is created, updated or deleted
//   })

export const useWebsocketStore = defineStore({
  id: 'websocket-store',

  /**
   * All state can be set in here
   */
  state: (): RootState => {
    return {
      pusher: null,
      echo: null,
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
    init() {
      if (import.meta.env.VITE_PUSHER_ENABLED === 'true') {
        this.pusher = Pusher
        this.echo = new Echo({
          broadcaster: 'pusher',
          key: import.meta.env.VITE_PUSHER_APP_KEY,
          wsHost: import.meta.env.VITE_PUSHER_HOST,
          wsPort: import.meta.env.VITE_PUSHER_WS_PORT,
          wssPort: import.meta.env.VITE_PUSHER_WSS_PORT,
          forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? 'https') === 'https',
          enabledTransports: ['ws', 'wss'],
          disableStats: true,
          encrypted: true,
          cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
          auth: {
            headers: {
              Authorization: localStorage.getItem('_token'),
            },
          },
        })
      }
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
     * @param theState
     */
    getValue: (theState: RootState) => {
      return (path: string) => ObjectReader(theState, path)
    },
  },
})
