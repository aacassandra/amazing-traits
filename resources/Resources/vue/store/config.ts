import { defineStore } from 'pinia'
import { ObjectReader, ObjectUpdater } from '~/services'

export interface AppStyles {
  header_bg: string
  header_bg_dark: string
  header_text: string
  header_text_dark: string
  logo_text: string
  logo_text_dark: string
  sidebar_bg: string
  sidebar_bg_dark: string
  sidebar_border: string
  sidebar_border_dark: string
  sidebar_text: string
  sidebar_text_dark: string
}

export interface AppStyle {
  logo: {
    text: {
      light: string
      dark: string
    }
  }
  sidebar: {
    bg: {
      light: string
      dark: string
    }
    text: {
      light: string
      dark: string
    }
    border: {
      light: string
      dark: string
    }
  }
  header: {
    bg: {
      light: string
      dark: string
    }
    text: {
      light: string
      dark: string
    }
  }
}

export interface RootState {
  pagedrop: {
    enabled: boolean
    fadeDuration: number
  }
  backdrop: {
    enabled: boolean
    fadeDuration: number
  }
  disabled: {
    scroll: boolean
  }
  styleId: string
  style: AppStyle
}

export const useConfigStore = defineStore({
  id: 'config-store',

  /**
   * All state can be set in here
   */
  state: (): RootState => {
    return {
      pagedrop: {
        enabled: true,
        fadeDuration: 300,
      },
      backdrop: {
        enabled: false,
        fadeDuration: 400,
      },
      disabled: {
        scroll: false, // scroll of body section
      },
      styleId: '',
      style: {
        logo: {
          text: {
            light: 'text-gray-800',
            dark: 'text-gray-200',
          },
        },
        sidebar: {
          bg: {
            light: 'bg-base-100',
            dark: '',
          },
          text: {
            light: 'text-gray-800',
            dark: 'text-gray-200',
          },
          border: {
            light: 'border-gray-200',
            dark: 'border-gray-600',
          },
        },
        header: {
          bg: {
            light: 'bg-base-100',
            dark: '',
          },
          text: {
            light: 'text-gray-800',
            dark: 'text-gray-200',
          },
        },
      },
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
    save(styles: any) {
      const template = {
        logo: {
          text: {
            light: styles.logo.text.light,
            dark: styles.logo.text.dark,
          },
        },
        sidebar: {
          bg: {
            light: styles.sidebar.bg.light,
            dark: styles.sidebar.bg.dark,
          },
          text: {
            light: styles.sidebar.text.light,
            dark: styles.sidebar.text.dark,
          },
          border: {
            light: styles.sidebar.border.light,
            dark: styles.sidebar.border.dark,
          },
        },
        header: {
          bg: {
            light: styles.header.bg.light,
            dark: styles.header.bg.dark,
          },
          text: {
            light: styles.header.text.light,
            dark: styles.header.text.dark,
          },
        },
      }
      localStorage.setItem('_style', JSON.stringify(template))
      this.style = template
    },
    reset() {
      return new Promise((res) => {
        this.style = {
          logo: {
            text: {
              light: 'text-gray-800',
              dark: 'text-gray-200',
            },
          },
          sidebar: {
            bg: {
              light: 'bg-base-100',
              dark: '',
            },
            text: {
              light: 'text-gray-800',
              dark: 'text-gray-200',
            },
            border: {
              light: 'border-gray-200',
              dark: 'border-gray-600',
            },
          },
          header: {
            bg: {
              light: 'bg-base-100',
              dark: '',
            },
            text: {
              light: 'text-gray-800',
              dark: 'text-gray-200',
            },
          },
        }
        localStorage.setItem('_style', this.style)
        res(this.style)
      })
    },
    init() {
      return new Promise((res) => {
        const style: any = localStorage.getItem('_style')
        if (!style) {
          localStorage.setItem('_style', JSON.stringify(this.style))
        } else {
          this.style = JSON.parse(style)
        }

        res(true)
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
     * @param theState
     */
    getValue: (theState: RootState) => {
      return (path: string) => ObjectReader(theState, path)
    },
    getStyle(): AppStyle {
      return this.style
    },
  },
})
