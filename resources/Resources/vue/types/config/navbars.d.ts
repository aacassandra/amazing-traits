export interface NavMainProfile {
  type: 'profile';
  avatar: string;
  name: string;
  email: string;
}

export interface NavMainTextLink {
  type: 'text-link';
  icon: string;
  text: string;
  route: string;
}

export interface NavMainSignOut {
  type: 'sign-out';
  icon: string;
  text: string;
}

export interface NavMain {
  type: 'main';
  avatar: string;
  short_name: string;
  active: boolean;
  items: Array<NavMainProfile | NavMainTextLink | NavMainSignOut>;
}

export interface NavNotificationsLabel {
  type: 'label';
  text: string;
}

export interface NavNotificationsItem {
  type: 'item';
  icon: string;
  iconColor: string;
  title: string;
  createdAt: string;
  route: string;
}

export interface NavNotificationsLoadMore {
  type: 'load-more';
  text: string;
  route: string;
}

export interface NavNotification {
  type: 'notification';
  icon: string;
  availableNew: boolean;
  active: boolean;
  items: Array<
    NavNotificationsLabel | NavNotificationsItem | NavNotificationsLoadMore
  >;
}
