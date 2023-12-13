import { defineStore } from 'pinia'
import { ObjectReader, ObjectUpdater, SyncronizeMenu } from '~/services'
import {
  SidebarsDropdown,
  SidebarsGroup,
  SidebarsTextLink,
} from '~/types/components/organism/sidebar'
import axios from 'axios'

const permissions = {
  setup: [
    'setup-master-general-view',
    'setup-master-packages',
    'setup-master-features',
    'setup-master-news-view',
    'setup-master-categories',
    'setup-master-menu',
    'setup-master-epic',
    'setup-master-approval',
    'setup-master-housings-view',
    'setup-master-clusters-view',
    'setup-master-users-view',
    'setup-master-roles-view',
  ],
}

export interface RootState {
  sidebars: Array<SidebarsGroup | SidebarsTextLink | SidebarsDropdown>
}

const useSidebarsStore = defineStore({
  id: 'sidebar-store',

  /**
   * All state can be set in here
   */
  state: (): RootState => {
    return {
      sidebars: [
        {
          type: 'groupname',
          text: 'Menu',
          permissions: ['dashboard'],
        },
        {
          id: 'dashboard',
          type: 'text-link',
          text: 'Dashboard',
          icon: '<i class="fa-light fa-home"></i>',
          route: '/dashboard',
          active: false,
          hide: false,
          permissions: ['dashboard-view'],
        },
        {
          id: 'approval',
          type: 'text-link',
          text: 'Approval',
          lang: 'menu.approval',
          icon: '<i class="fa-light fa-person-circle-check"></i>',
          route: '/approval',
          permissions: ['approval-view'],
          active: false,
          maintenance: false,
        },
        {
          type: 'dropdown',
          text: 'Setup',
          icon: '<i class="fa-light fa-layer-group"></i>',
          active: false,
          permissions: permissions.setup,
          items: [
            {
              type: 'dropdown',
              text: 'Master',
              icon: '<i class="fa-light fa-database"></i>',
              active: false,
              permissions: permissions.setup,
              items: [
                {
                  id: 'setup-master-general',
                  type: 'text-link',
                  text: 'General',
                  lang: 'menu.general',
                  icon: '<i class="fa-light fa-cabinet-filing"></i>',
                  route: '/setup/master/general',
                  active: false,
                  permissions: ['setup-master-general-view'],
                  maintenance: false,
                  hide: false,
                },
                {
                  id: 'setup-master-packages',
                  type: 'text-link',
                  text: 'Packages',
                  lang: 'menu.packages',
                  icon: '<i class="fa-light fa-boxes-packing"></i>',
                  route: '/setup/master/packages',
                  active: false,
                  permissions: ['setup-master-packages-view'],
                  maintenance: false,
                  hide: false,
                },
                {
                  id: 'setup-master-features',
                  type: 'text-link',
                  text: 'Features',
                  lang: 'menu.features',
                  icon: '<i class="fa-light fa-list-check"></i>',
                  route: '/setup/master/features',
                  active: false,
                  permissions: ['setup-master-features-view'],
                  maintenance: false,
                  hide: false,
                },
                {
                  id: 'setup-master-categories',
                  type: 'text-link',
                  text: 'Categories',
                  lang: 'menu.categories',
                  icon: '<i class="fa-light fa-sitemap"></i>',
                  route: '/setup/master/categories',
                  active: false,
                  permissions: ['setup-master-categories-view'],
                  maintenance: false,
                  hide: false,
                },
                {
                  id: 'setup-master-news',
                  type: 'text-link',
                  text: 'News',
                  lang: 'menu.news',
                  icon: '<i class="fa-light fa-newspaper"></i>',
                  route: '/setup/master/news',
                  active: false,
                  permissions: ['setup-master-news-view'],
                  maintenance: false,
                  hide: false,
                },
                {
                  id: 'setup-master-menu',
                  type: 'text-link',
                  text: 'Menu',
                  lang: 'menu.menu',
                  icon: '<i class="fa-light fa-list-dropdown"></i>',
                  route: '/setup/master/menu',
                  active: false,
                  permissions: ['setup-master-menu-view'],
                  maintenance: false,
                  hide: false,
                },
                {
                  id: 'setup-master-epic',
                  type: 'text-link',
                  text: 'Epic',
                  lang: 'menu.epic',
                  icon: '<i class="fa-light fa-bolt-lightning"></i>',
                  route: '/setup/master/epic',
                  active: false,
                  permissions: ['setup-master-epic-view'],
                  maintenance: false,
                  hide: false,
                },
                {
                  id: 'setup-master-approval',
                  type: 'text-link',
                  text: 'Approval',
                  lang: 'menu.approval',
                  icon: '<i class="fa-light fa-timeline-arrow"></i>',
                  route: '/setup/master/approval',
                  active: false,
                  permissions: ['setup-master-approval-view'],
                  maintenance: false,
                  hide: false,
                },
                {
                  id: 'setup-master-housings',
                  type: 'text-link',
                  text: 'Housings',
                  lang: 'menu.housings',
                  icon: '<i class="fa-light fa-house-flag"></i>',
                  route: '/setup/master/housings',
                  active: false,
                  permissions: ['setup-master-housings-view'],
                  maintenance: false,
                  hide: false,
                },
                {
                  id: 'setup-master-clusters',
                  type: 'text-link',
                  text: 'Clusters',
                  lang: 'menu.clusters',
                  icon: '<i class="fa-light fa-house"></i>',
                  route: '/setup/master/clusters',
                  active: false,
                  permissions: ['setup-master-clusters-view'],
                  maintenance: false,
                  hide: false,
                },
                {
                  id: 'setup-master-users',
                  type: 'text-link',
                  text: 'Users',
                  lang: 'menu.users',
                  icon: '<i class="fa-light fa-users"></i>',
                  route: '/setup/master/users',
                  active: false,
                  permissions: ['setup-master-users-view'],
                  maintenance: false,
                },
                {
                  id: 'setup-master-roles',
                  type: 'text-link',
                  text: 'Roles',
                  icon: '<i class="fa-light fa-user-shield"></i>',
                  route: '/setup/master/roles',
                  active: false,
                  permissions: ['setup-master-roles-view'],
                  maintenance: false,
                },
              ],
            },
            {
              type: 'dropdown',
              text: 'Transactions',
              lang: 'menu.transactions',
              icon: '<i class="fa-light fa-inbox"></i>',
              active: false,
              permissions: [],
              items: [
                {
                  id: 'setup-transactions-backlog',
                  type: 'text-link',
                  text: 'Backlog',
                  icon: '<i class="fa-light fa-calendar-clock"></i>',
                  route: '/setup/transactions/backlog',
                  active: false,
                  permissions: [],
                  maintenance: false,
                  hide: false,
                },
              ],
            },
          ],
        },
        {
          type: 'dropdown',
          text: 'Subscriptions',
          lang: 'menu.subscriptions',
          icon: '<i class="fa-light fa-calendar-circle-user"></i>',
          active: false,
          permissions: ['subscriptions-transactions-packages-view'],
          items: [
            {
              type: 'dropdown',
              text: 'Transactions',
              lang: 'menu.transactions',
              icon: '<i class="fa-light fa-inbox"></i>',
              active: false,
              permissions: ['subscriptions-transactions-packages-view'],
              items: [
                {
                  id: 'subscriptions-transactions-packages',
                  type: 'text-link',
                  text: 'Packages',
                  lang: 'menu.packages',
                  icon: '<i class="fa-light fa-boxes-packing"></i>',
                  route: '/subscriptions/transactions/packages',
                  active: false,
                  permissions: ['subscriptions-transactions-packages-view'],
                  maintenance: false,
                  hide: false,
                },
                {
                  id: 'history-subscriptions-transactions-packages',
                  type: 'text-link',
                  text: 'History',
                  lang: 'menu.history_subscription',
                  icon: '<i class="fa-light fa-clock-rotate-left"></i>',
                  route: '/subscriptions/transactions/history-subscription',
                  active: false,
                  permissions: [],
                  maintenance: false,
                  hide: false,
                },
              ],
            },
          ],
        },
        {
          id: 'chats',
          type: 'text-link',
          text: 'Chats',
          icon: '<i class="fa-light fa-comments"></i>',
          route: '/chats',
          active: false,
          hide: true,
        },
        {
          type: 'dropdown',
          text: 'Worktask',
          icon: '<i class="fa-light fa-list-check"></i>',
          active: false,
          permissions: [
            'worktask-menu-mobile_application-view',
            'worktask-menu-landing_page-view',
            'worktask-menu-backoffice-view',
            'worktask-menu-api_service-view',
          ],
          items: [],
        },
        {
          id: 'form_example',
          type: 'text-link',
          text: 'Form Example',
          icon: '<i class="fa-light fa-list-alt"></i>',
          route: '/form/example',
          active: false,
          hide: false,
          permissions: ['form_example-view'],
        },

        // for admin
        {
          type: 'dropdown',
          text: 'Master',
          icon: '<i class="fa-light fa-database"></i>',
          active: false,
          permissions: [
            'cluster-master-approval-view',
            'cluster-master-securities-view',
            'cluster-master-admins-view',
            'cluster-master-incentives-view',
          ],
          items: [
            {
              id: 'cluster-master-approval',
              type: 'text-link',
              text: 'Approval',
              lang: 'menu.approval',
              icon: '<i class="fa-light fa-timeline-arrow"></i>',
              route: '/cluster/master/approval',
              active: false,
              permissions: ['cluster-master-approval-view'],
              maintenance: false,
            },
            {
              id: 'cluster-master-securities',
              type: 'text-link',
              text: 'Securities',
              lang: 'menu.securities',
              icon: '<i class="fa-light fa-user-police-tie"></i>',
              route: '/cluster/master/securities',
              active: false,
              permissions: ['cluster-master-securities-view'],
              maintenance: false,
            },
            {
              id: 'cluster-master-admins',
              type: 'text-link',
              text: 'Admins',
              lang: 'menu.admins',
              icon: '<i class="fa-light fa-user-tie-hair"></i>',
              route: '/cluster/master/admins',
              active: false,
              permissions: ['cluster-master-admins-view'],
              maintenance: false,
            },
            {
              id: 'cluster-master-incentives',
              type: 'text-link',
              text: 'Incentives',
              lang: 'menu.incentives',
              icon: '<i class="fa-light fa-hands-holding-dollar"></i>',
              route: '/cluster/master/incentives',
              active: false,
              permissions: ['cluster-master-incentives-view'],
              maintenance: false,
              hide: false,
            },
          ],
        },
        {
          type: 'dropdown',
          text: 'Reports',
          lang: 'menu.reports',
          icon: '<i class="fa-light fa-inbox"></i>',
          active: false,
          permissions: [
            'cluster-reports-patrol_schedule-view',
            'cluster-reports-attendances-view',
            'cluster-reports-visitors-view',
            'cluster-reports-reports-view',
            'cluster-reports-patrols-view',
          ],
          items: [
            {
              id: 'cluster-reports-patrol_schedule',
              type: 'text-link',
              text: 'Patrol Schedule',
              icon: '<i class="fa-light fa-calendar-circle-user"></i>',
              route: '#',
              active: false,
              permissions: ['cluster-reports-patrol_schedule-view'],
              maintenance: true,
              hide: true,
            },
            {
              id: 'cluster-reports-attendances',
              type: 'text-link',
              text: 'Attendances',
              lang: 'menu.attendances',
              icon: '<i class="fa-light fa-clipboard-check"></i>',
              route: '/cluster/reports/attendances',
              permissions: ['cluster-reports-attendances-view'],
              active: false,
              maintenance: false,
            },
            {
              id: 'cluster-reports-absences',
              type: 'text-link',
              text: 'Absence Permit',
              lang: 'menu.absence_permit',
              icon: '<i class="fa-light fa-clipboard-check"></i>',
              route: '/cluster/reports/absences',
              permissions: ['cluster-reports-absences-view'],
              active: false,
              maintenance: false,
            },
            {
              id: 'cluster-reports-visitors',
              type: 'text-link',
              text: 'Visitors',
              lang: 'menu.visitors',
              icon: '<i class="fa-light fa-user-group"></i>',
              route: '/cluster/reports/visitors',
              permissions: ['cluster-reports-visitors-view'],
              active: false,
              maintenance: false,
            },
            {
              id: 'cluster-reports-reports',
              type: 'text-link',
              text: 'Reports',
              lang: 'menu.reports',
              icon: '<i class="fa-light fa-image-polaroid"></i>',
              route: '/cluster/reports/reports',
              permissions: ['cluster-reports-reports-view'],
              active: false,
              maintenance: false,
            },
            {
              id: 'cluster-reports-patrols',
              type: 'text-link',
              text: 'Patrols',
              lang: 'menu.patrols',
              icon: '<i class="fa-light fa-map-location-dot"></i>',
              route: '/cluster/reports/patrols',
              permissions: ['cluster-reports-patrols-view'],
              active: false,
              maintenance: false,
            },
          ],
        },
        {
          type: 'dropdown',
          text: 'Calculation',
          icon: '<i class="fa-light fa-calculator"></i>',
          active: false,
          permissions: ['calculation-point_rewards-view'],
          items: [
            {
              id: 'calculation-point_rewards',
              type: 'text-link',
              text: 'Point Reward',
              icon: '<i class="fa-light fa-trophy"></i>',
              route: '/calculation/point-rewards',
              active: false,
              permissions: ['calculation-point_rewards-view'],
              maintenance: false,
              hide: false,
            },
          ],
        },
        {
          id: 'app_reports',
          type: 'text-link',
          text: 'Application Reports',
          lang: 'menu.app_reports',
          icon: '<i class="fa-light fa-bug"></i>',
          route: '/app-reports',
          active: false,
          permissions: ['app_reports-view'],
          maintenance: false,
          hide: false,
        },
        {
          id: 'cluster-settings',
          type: 'text-link',
          text: 'Settings',
          lang: 'menu.settings',
          icon: '<i class="fa-light fa-cogs"></i>',
          route: '/cluster/settings',
          active: false,
          permissions: ['cluster-settings-view'],
          maintenance: false,
        },

        {
          id: 'websockets',
          type: 'text-link',
          text: 'Websockets',
          icon: '<i class="fa-light fa-rocket"></i>',
          route: '/websockets',
          active: false,
          hide: false,
          permissions: ['websockets-view'],
        },
        {
          id: 'settings',
          type: 'text-link',
          text: 'Settings',
          icon: '<i class="fa-light fa-cogs"></i>',
          route: '/settings',
          permissions: ['settings-view'],
          active: false,
          maintenance: false,
        },
      ],
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
    setSidebar(value) {
      ObjectUpdater(this, 'sidebars', value)
    },
    getWorktaskMenu() {
      const cookieToken = localStorage.getItem('_token')
      const table_prefix = import.meta.env.VITE_APP_TABLE_PREFIX
      const params = {
        where: "`group`='STACK CATEGORY'",
      }
      return new Promise((resolve) => {
        axios(
          `/api/v1/${table_prefix}m_general?` + new URLSearchParams(params),
          {
            method: 'GET',
            headers: {
              Authorization: cookieToken,
              'Content-Type': 'application/json',
            },
          },
        ).then((response) => {
          const items = []
          let worktaskIndexPosition = 0
          this.sidebars.forEach((sidebar, sidebarI) => {
            if (sidebar.type === 'dropdown' && sidebar.text === 'Worktask') {
              worktaskIndexPosition = sidebarI
            }
          })
          response.data.data.forEach((item, index) => {
            items.push({
              id:
                'worktask-menu-' + item.value_1.replace(' ', '_').toLowerCase(),
              type: 'text-link',
              text: item.value_1,
              icon: item.value_3,
              route: `/worktask/${item.value_2}`,
              active: false,
              permissions: [
                `worktask-menu-${item.value_1
                  .replace(' ', '_')
                  .toLowerCase()}-view`,
              ],
              maintenance: false,
              hide: false,
            })
          })
          ObjectUpdater(this, `sidebars.${worktaskIndexPosition}.items`, items)

          SyncronizeMenu(items, true)
          resolve(response)
        })
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
    getSidebars: (theState: RootState) => {
      return theState.sidebars
    },
  },
})

export { useSidebarsStore }
