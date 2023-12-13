import { NavMain, NavNotification } from '~/types/config/navbars'

const navbars: Array<NavMain | NavNotification> = [
  {
    type: 'main',
    avatar: 'http://daisyui.com/tailwind-css-component-profile-1@40w.png',
    short_name: '',
    active: true,
    items: [
      {
        type: 'profile',
        avatar: 'http://daisyui.com/tailwind-css-component-profile-1@40w.png',
        name: '',
        email: '',
      },
      {
        type: 'text-link',
        icon: 'fad fa-users',
        text: 'Profile',
        route: '#',
      },
      {
        type: 'text-link',
        icon: 'fad fa-cogs',
        text: 'Settings',
        route: '#',
      },
      {
        type: 'sign-out',
        icon: 'fad fa-sign-out',
        text: 'Sign Out',
      },
    ],
  },
  {
    type: 'notification',
    icon: 'fad fa-bell',
    availableNew: true,
    active: true,
    items: [
      {
        type: 'label',
        text: 'Notifications',
      },
      {
        type: 'item',
        icon: 'fas fa-check-circle',
        iconColor: 'text-green-600',
        title: 'You have new follower',
        createdAt: '15 min ago',
        route: '#',
      },
      {
        type: 'item',
        icon: 'fas fa-plus-circle',
        iconColor: 'text-blue-600',
        title: 'New sale, keep it up',
        createdAt: '19 min ago',
        route: '#',
      },
      {
        type: 'item',
        icon: 'fas fa-times',
        iconColor: 'text-red-600',
        title: 'Update failed, restart server',
        createdAt: '23 min ago',
        route: '#',
      },
      {
        type: 'load-more',
        text: 'Load More',
        route: '#',
      },
    ],
  },
]

export default navbars
