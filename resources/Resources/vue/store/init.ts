import { defineStore } from 'pinia'
import { ObjectReader, ObjectUpdater } from '~/services'

export interface RootState {
  notyf: any
}

export const useInitStore = defineStore({
  id: 'init-store',

  /**
   * All state can be set in here
   */
  state: (): RootState => {
    return {
      notyf: null,
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
    setValue(path: string, value: any, push = false) {
      ObjectUpdater(this, path, value, push)
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
