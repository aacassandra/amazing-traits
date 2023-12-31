import {
  SidebarsDropdown,
  SidebarsGroup,
  SidebarsTextLink,
} from '~/types/components/organism/sidebar'

const permissions = {
  setup: [
    'setup-master-general-view',
    'setup-master-packages-view',
    'setup-master-features-view',
    'setup-master-news-view',
    'setup-master-menu-view',
    'setup-master-users-view',
    'setup-master-securities-view',
    'setup-master-roles-view',
    'setup-master-housings-view',
    'setup-master-clusters-view',
    'setup-master-stores-view',
    'setup-master-patrols-view',
  ],
}

const sidebars: Array<SidebarsGroup | SidebarsTextLink | SidebarsDropdown> = [
  {
    type: 'groupname',
    text: 'Menu',
    permissions: [, 'dashboard'],
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
    id: 'notifications',
    type: 'text-link',
    text: 'Notifications',
    lang: 'menu.notifications',
    icon: '<i class="fa-light fa-bell"></i>',
    route: '/notifications',
    permissions: ['notification-view'],
    active: false,
    maintenance: true,
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
    ],
  },
  {
    type: 'dropdown',
    text: 'Security',
    lang: 'menu.security',
    icon: '<i class="fa-light fa-user-police"></i>',
    active: false,
    permissions: [
      'security-transactions-attendances-view',
      'security-transactions-visitors-view',
      'security-transactions-reports-view',
      'security-transactions-patrols-view',
    ],
    items: [
      {
        type: 'dropdown',
        text: 'Transactions',
        lang: 'menu.transactions',
        icon: '<i class="fa-light fa-inbox"></i>',
        active: false,
        permissions: [
          'security-transactions-attendances-view',
          'security-transactions-visitors-view',
          'security-transactions-reports-view',
          'security-transactions-patrols-view',
        ],
        items: [
          {
            id: 'security-transactions-patrol_schedule',
            type: 'text-link',
            text: 'Patrol Schedule',
            icon: '<i class="fa-light fa-calendar-circle-user"></i>',
            route: '#',
            active: false,
            permissions: [],
            maintenance: true,
            hide: true,
          },
          {
            id: 'security-transactions-attendances',
            type: 'text-link',
            text: 'Attendances',
            lang: 'menu.attendances',
            icon: '<i class="fa-light fa-clipboard-check"></i>',
            route: '/security/transactions/attendances',
            permissions: ['security-transactions-attendances-view'],
            active: false,
            maintenance: false,
          },
          {
            id: 'security-transactions-visitors',
            type: 'text-link',
            text: 'Visitors',
            lang: 'menu.visitors',
            icon: '<i class="fa-light fa-user-group"></i>',
            route: '/security/transactions/visitors',
            permissions: ['security-transactions-visitors-view'],
            active: false,
            maintenance: false,
          },
          {
            id: 'security-transactions-reports',
            type: 'text-link',
            text: 'Reports',
            lang: 'menu.reports',
            icon: '<i class="fa-light fa-image-polaroid"></i>',
            route: '/security/transactions/reports',
            permissions: [],
            active: false,
            maintenance: false,
          },
          {
            id: 'security-transactions-patrols',
            type: 'text-link',
            text: 'Patrols',
            lang: 'menu.patrols',
            icon: '<i class="fa-light fa-map-location-dot"></i>',
            route: '/security/transactions/patrols',
            permissions: ['security-transactions-patrols-view'],
            active: false,
            maintenance: false,
          },
        ],
      },
    ],
  },
  {
    type: 'dropdown',
    text: 'Cluster',
    lang: 'menu.cluster',
    icon: '<i class="fa-light fa-house"></i>',
    active: false,
    permissions: [
      'cluster-settings',
      'cluster-master-securities-view',
      'cluster-master-admins-view',
    ],
    items: [
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
        type: 'dropdown',
        text: 'Master',
        icon: '<i class="fa-light fa-database"></i>',
        active: false,
        permissions: [
          'cluster-master-securities-view',
          'cluster-master-admins-view',
        ],
        items: [
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
            id: 'cluster-transactions-app_reports',
            type: 'text-link',
            text: 'Application Reports',
            lang: 'menu.app_reports',
            icon: '<i class="fa-light fa-bug"></i>',
            route: '/cluster/transaction/report',
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
    text: 'Packages',
    lang: 'menu.packages',
    icon: '<i class="fa-light fa-boxes-packing"></i>',
    active: false,
    permissions: ['packages-transactions-subscriptions-view'],
    items: [
      {
        type: 'dropdown',
        text: 'Transactions',
        lang: 'menu.transactions',
        icon: '<i class="fa-light fa-inbox"></i>',
        active: false,
        permissions: ['packages-transactions-subscriptions-view'],
        items: [
          {
            id: 'packages-transactions-subscriptions',
            type: 'text-link',
            text: 'Subscriptions',
            lang: 'menu.subscriptions',
            icon: '<i class="fa-light fa-calendar-circle-user"></i>',
            route: '#',
            active: false,
            permissions: ['packages-transactions-subscriptions-view'],
            maintenance: true,
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
    id: 'form_example',
    type: 'text-link',
    text: 'Form Example',
    icon: '<i class="fa-light fa-list-alt"></i>',
    route: '/form/example',
    active: false,
    hide: false,
  },
  {
    id: 'settings',
    type: 'text-link',
    text: 'Settings',
    lang: 'menu.settings',
    icon: '<i class="fa-light fa-cogs"></i>',
    route: '/settings',
    permissions: [],
    active: false,
    maintenance: false,
  },
]

export default sidebars
