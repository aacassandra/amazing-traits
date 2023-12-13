import { defineStore } from 'pinia'
import { ObjectReader, ObjectUpdater } from '~/services'

export interface RootState {
  modal: number
}

export const useCounterStore = defineStore({
  id: 'counter-store',
  state: (): RootState => {
    return {
      modal: 0,
    }
  },
  actions: {
    setValue(path: string, value: any, push = false) {
      ObjectUpdater(this, path, value, push)
    },
    increment() {
      this.modal++
    },
    decrement() {
      this.modal--
    },
  },
  getters: {
    getValue: (theState: RootState) => {
      return (path: string) => ObjectReader(theState, path)
    },
  },
})
