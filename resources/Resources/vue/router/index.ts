import { createRouter, createWebHistory, RouteRecordRaw } from 'vue-router'
import Auths from '~/pages/auths'
import Dashboard from '~/pages/dashboard.vue'
import Approval from '~/pages/approval.vue'
import FormExample from '~/pages/form/example.vue'
import Settings from '~/pages/settings/index.vue'
import Guard from './guard'
import SetupMasterGeneral from '~/pages/setup/master/general'
import SetupMasterPackages from '~/pages/setup/master/packages'
import SetupMasterNews from '~/pages/setup/master/news'
import SetupMasterFeatures from '~/pages/setup/master/features'
import SetupMasterCategories from '~/pages/setup/master/categories'
import SetupMasterMenu from '~/pages/setup/master/menu'
import SetupMasterEpic from '~/pages/setup/master/epic'
import SetupMasterApproval from '~/pages/setup/master/approval'
import SetupMasterHousings from '~/pages/setup/master/housings'
import SetupMasterClusters from '~/pages/setup/master/clusters'
import SetupMasterStores from '~/pages/setup/master/stores'
import SetupMasterUsers from '~/pages/setup/master/users'
import SetupMasterRoles from '~/pages/setup/master/roles'
import SetupTransactionsBacklog from '~/pages/setup/transactions/backlog'
import SubscriptionsTransactionsPackages from '~/pages/subscriptions/transactions/packages.vue'
import HistorySubscriptionsTransactionsPackages from '~/pages/subscriptions/transactions/RiwayatLangganan.vue'
import ClusterSettings from '~/pages/cluster/settings/index.vue'
import ClusterMasterApproval from '~/pages/cluster/master/approval'
import ClusterMasterSecurities from '~/pages/cluster/master/securities'
import ClusterMasterAdmins from '~/pages/cluster/master/admins'
import ClusterMasterIncentives from '~/pages/cluster/master/incentives'
import ClusterReportsVisitors from '~/pages/cluster/reports/visitors'
import ClusterReportsAttendances from '~/pages/cluster/reports/attendances'
import ClusterReportsAbsences from '~/pages/cluster/reports/absences'
import ClusterReportsReports from '~/pages/cluster/reports/reports'
import ClusterReportsPatrols from '~/pages/cluster/reports/patrols'
import AppReports from '~/pages/app-reports'
import Worktask from '~/pages/worktask/index.vue'
import Websockets from '~/pages/websockets.vue'
import CalculationPointRewards from '~/pages/calculation/point_rewards.vue'
import DashboardPublic from '~/pages/dashboard-public.vue'

import Chats from '~/pages/chats.vue'

const routes: Array<RouteRecordRaw> = [
  {
    path: '',
    meta: {
      layout: 'Default',
    },
    children: [
      {
        path: '/',
        name: 'signin',
        component: Auths.Signin,
      },
      {
        path: '/signup',
        name: 'signup',
        component: Auths.Signup,
      },
      {
        path: '/signup-verification',
        name: 'signup-verification',
        component: Auths.SignupVerification,
      },
      {
        path: '/account-verification',
        name: 'account-verification',
        component: Auths.AccountVerification,
      },
      {
        path: '/otp-verification',
        name: 'otp-verification',
        component: Auths.OtpVerification,
      },
      {
        path: '/confirmation-delete',
        name: 'confirmation-delete',
        component: Auths.Confirmation,
      },
      {
        path: '/delete-success',
        name: 'delete-success',
        component: Auths.Success,
      },
      {
        path: '/cluster-verification',
        name: 'cluster-verification',
        component: Auths.ClusterVerification,
      },
      {
        path: '/forgot-password',
        name: 'forgot-password',
        component: Auths.ForgotPassword,
      },
      {
        path: '/forgot-password-verification',
        name: 'forgot-password-verification',
        component: Auths.ForgotVerification,
      },
      {
        path: '/change-password',
        name: 'change-password',
        component: Auths.ChangePassword,
      },
      {
        path: '/public-dashboard',
        name: 'public-dashboard',
        component: DashboardPublic,
      },
    ],
  },
  {
    path: '/dashboard',
    name: 'dashboard',
    component: Dashboard,
    meta: {
      layout: 'Dashboard',
      requiresAuth: true,
    },
  },
  {
    path: '/approval',
    name: 'approval',
    component: Approval,
    meta: {
      layout: 'Dashboard',
      requiresAuth: true,
    },
  },
  {
    path: '/form/example',
    name: 'form-example',
    component: FormExample,
    meta: {
      layout: 'Dashboard',
      requiresAuth: true,
    },
  },
  {
    path: '/chats',
    name: 'chats',
    component: Chats,
    meta: {
      layout: 'Dashboard',
      requiresAuth: true,
    },
  },
  {
    path: '/setup',
    meta: {
      layout: 'Dashboard',
      requiresAuth: true,
    },
    children: [
      {
        path: 'master/general',
        children: [
          {
            path: '',
            name: 'setup-master-general',
            component: SetupMasterGeneral.Landing,
          },
          {
            path: 'form',
            name: 'setup-master-general-form',
            component: SetupMasterGeneral.Form,
          },
        ],
      },
      {
        path: 'master/packages',
        children: [
          {
            path: '',
            name: 'setup-master-packages',
            component: SetupMasterPackages.Landing,
          },
          {
            path: 'form',
            name: 'setup-master-packages-form',
            component: SetupMasterPackages.Form,
          },
        ],
      },
      {
        path: 'master/news',
        children: [
          {
            path: '',
            name: 'setup-master-news',
            component: SetupMasterNews.Landing,
          },
          {
            path: 'form',
            name: 'setup-master-news-form',
            component: SetupMasterNews.Form,
          },
        ],
      },
      {
        path: 'master/features',
        children: [
          {
            path: '',
            name: 'setup-master-features',
            component: SetupMasterFeatures.Landing,
          },
          {
            path: 'form',
            name: 'setup-master-features-form',
            component: SetupMasterFeatures.Form,
          },
        ],
      },
      {
        path: 'master/categories',
        children: [
          {
            path: '',
            name: 'setup-master-categories',
            component: SetupMasterCategories.Landing,
          },
          {
            path: 'form',
            name: 'setup-master-categories-form',
            component: SetupMasterCategories.Form,
          },
        ],
      },
      {
        path: 'master/menu',
        children: [
          {
            path: '',
            name: 'setup-master-menu',
            component: SetupMasterMenu.Landing,
          },
          {
            path: 'form',
            name: 'setup-master-menu-form',
            component: SetupMasterMenu.Form,
          },
        ],
      },
      {
        path: 'master/categories',
        children: [
          {
            path: '',
            name: 'setup-master-categories',
            component: SetupMasterCategories.Landing,
          },
          {
            path: 'form',
            name: 'setup-master-categories-form',
            component: SetupMasterCategories.Form,
          },
        ],
      },
      {
        path: 'master/epic',
        children: [
          {
            path: '',
            name: 'setup-master-epic',
            component: SetupMasterEpic.Landing,
          },
          {
            path: 'form',
            name: 'setup-master-epic-form',
            component: SetupMasterEpic.Form,
          },
        ],
      },
      {
        path: 'master/approval',
        children: [
          {
            path: '',
            name: 'setup-master-approval',
            component: SetupMasterApproval.Landing,
          },
          {
            path: 'form',
            name: 'setup-master-approval-form',
            component: SetupMasterApproval.Form,
          },
        ],
      },
      {
        path: 'master/housings',
        children: [
          {
            path: '',
            name: 'setup-master-housings',
            component: SetupMasterHousings.Landing,
          },
          {
            path: 'form',
            name: 'setup-master-housings-form',
            component: SetupMasterHousings.Form,
          },
        ],
      },
      {
        path: 'master/clusters',
        children: [
          {
            path: '',
            name: 'setup-master-clusters',
            component: SetupMasterClusters.Landing,
          },
          {
            path: 'form',
            name: 'setup-master-clusters-form',
            component: SetupMasterClusters.Form,
          },
        ],
      },
      {
        path: 'master/stores',
        children: [
          {
            path: '',
            name: 'setup-master-stores',
            component: SetupMasterStores.Landing,
          },
          {
            path: 'form',
            name: 'setup-master-stores-form',
            component: SetupMasterStores.Form,
          },
        ],
      },
      {
        path: 'master/users',
        children: [
          {
            path: '',
            name: 'setup-master-users',
            component: SetupMasterUsers.Landing,
          },
          {
            path: 'form',
            name: 'setup-master-users-form',
            component: SetupMasterUsers.Form,
          },
        ],
      },
      {
        path: 'master/roles',
        children: [
          {
            path: '',
            name: 'setup-master-roles',
            component: SetupMasterRoles.Landing,
          },
          {
            path: 'form',
            name: 'setup-master-roles-form',
            component: SetupMasterRoles.Form,
          },
        ],
      },

      // transactions
      {
        path: 'transactions/backlog',
        children: [
          {
            path: '',
            name: 'setup-transactions-backlog',
            component: SetupTransactionsBacklog.Landing,
          },
          {
            path: 'form',
            name: 'setup-transactions-backlog-form',
            component: SetupTransactionsBacklog.Form,
          },
        ],
      },
    ],
  },
  {
    path: '/security',
    meta: {
      layout: 'Dashboard',
      requiresAuth: true,
    },
    children: [
      {
        path: 'transactions/visitors',
        children: [
          {
            path: '',
            name: 'security-visitors',
            component: ClusterReportsVisitors.Landing,
          },
          {
            path: 'form',
            name: 'security-visitors-form',
            component: ClusterReportsVisitors.Form,
          },
        ],
      },
      {
        path: 'transactions/attendances',
        children: [
          {
            path: '',
            name: 'security-attendances',
            component: ClusterReportsAttendances.Landing,
          },
        ],
      },
      {
        path: 'transactions/reports',
        children: [
          {
            path: '',
            name: 'security-reports',
            component: ClusterReportsReports.Landing,
          },
          {
            path: 'form',
            name: 'security-reports-form',
            component: ClusterReportsReports.Form,
          },
        ],
      },
      {
        path: 'transactions/patrols',
        children: [
          {
            path: '',
            name: 'security-patrols',
            component: ClusterReportsPatrols.Landing,
          },
          {
            path: 'form',
            name: 'security-patrols-form',
            component: ClusterReportsPatrols.Form,
          },
        ],
      },
    ],
  },
  {
    path: '/cluster',
    meta: {
      layout: 'Dashboard',
      requiresAuth: true,
    },
    children: [
      {
        path: 'settings',
        name: 'cluster-settings',
        component: ClusterSettings,
        meta: {
          layout: 'Dashboard',
          requiresAuth: true,
        },
      },
      {
        path: 'master/approval',
        children: [
          {
            path: '',
            name: 'cluster-master-approval',
            component: ClusterMasterApproval.Landing,
          },
          {
            path: 'form',
            name: 'cluster-master-approval-form',
            component: ClusterMasterApproval.Form,
          },
        ],
      },
      {
        path: 'master/securities',
        children: [
          {
            path: '',
            name: 'cluster-master-securities',
            component: ClusterMasterSecurities.Landing,
          },
          {
            path: 'form',
            name: 'cluster-master-securities-form',
            component: ClusterMasterSecurities.Form,
          },
        ],
      },
      {
        path: 'master/admins',
        children: [
          {
            path: '',
            name: 'cluster-master-admins',
            component: ClusterMasterAdmins.Landing,
          },
          {
            path: 'form',
            name: 'cluster-master-admins-form',
            component: ClusterMasterAdmins.Form,
          },
        ],
      },
      {
        path: 'master/incentives',
        children: [
          {
            path: '',
            name: 'cluster-master-incentives',
            component: ClusterMasterIncentives.Landing,
          },
          {
            path: 'form',
            name: 'cluster-master-incentives-form',
            component: ClusterMasterIncentives.Form,
          },
        ],
      },

      {
        path: 'reports/visitors',
        children: [
          {
            path: '',
            name: 'clusters-reports-visitors',
            component: ClusterReportsVisitors.Landing,
          },
          {
            path: 'form',
            name: 'clusters-reports-visitors-form',
            component: ClusterReportsVisitors.Form,
          },
        ],
      },
      {
        path: 'reports/absences',
        children: [
          {
            path: '',
            name: 'clusters-reports-absences',
            component: ClusterReportsAbsences.Landing,
          },
          {
            path: 'form',
            name: 'clusters-reports-absences-form',
            component: ClusterReportsAbsences.Form,
          },
        ],
      },
      {
        path: 'reports/attendances',
        children: [
          {
            path: '',
            name: 'clusters-reports-attendances',
            component: ClusterReportsAttendances.Landing,
          },
        ],
      },
      {
        path: 'reports/reports',
        children: [
          {
            path: '',
            name: 'clusters-reports-reports',
            component: ClusterReportsReports.Landing,
          },
          {
            path: 'form',
            name: 'clusters-reports-reports-form',
            component: ClusterReportsReports.Form,
          },
        ],
      },
      {
        path: 'reports/patrols',
        children: [
          {
            path: '',
            name: 'clusters-reports-patrols',
            component: ClusterReportsPatrols.Landing,
          },
          {
            path: 'form',
            name: 'clusters-reports-patrols-form',
            component: ClusterReportsPatrols.Form,
          },
        ],
      },
    ],
  },
  {
    path: '/subscriptions',
    meta: {
      layout: 'Dashboard',
      requiresAuth: true,
    },
    children: [
      {
        path: 'transactions/packages',
        children: [
          {
            path: '',
            name: 'subscriptions-transactions-packages',
            component: SubscriptionsTransactionsPackages,
          },
        ],
      },
      {
        path: 'transactions/history-subscription',
        children: [
          {
            path: '',
            name: 'history-subscriptions-transactions-packages',
            component: HistorySubscriptionsTransactionsPackages,
          },
        ],
      },
    ],
  },
  {
    path: '/app-reports',
    meta: {
      layout: 'Dashboard',
      requiresAuth: true,
    },
    children: [
      {
        path: '',
        name: 'app_reports',
        component: AppReports.Landing,
      },
      {
        path: 'form',
        name: 'app_reports-form',
        component: AppReports.Form,
      },
    ],
  },
  {
    path: '/worktask',
    meta: {
      layout: 'Dashboard',
      requiresAuth: true,
    },
    children: [
      // master
      {
        path: ':code',
        name: 'worktask',
        component: Worktask,
      },
    ],
  },
  {
    path: '/calculation',
    meta: {
      layout: 'Dashboard',
      requiresAuth: true,
    },
    children: [
      {
        path: 'point-rewards',
        name: 'point_rewards',
        component: CalculationPointRewards,
      },
    ],
  },
  {
    path: '/websockets',
    name: 'websockets',
    component: Websockets,
    meta: {
      layout: 'Dashboard',
      requiresAuth: true,
    },
  },
  {
    path: '/settings',
    name: 'settings',
    component: Settings,
    meta: {
      layout: 'Dashboard',
      requiresAuth: true,
    },
  },
]

const router = createRouter({
  history: createWebHistory(import.meta.env.VITE_BASE_URL || '/'),
  routes,
})

Guard(router)
export default router
