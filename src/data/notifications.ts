import { PageBreadcrumbItem } from 'components/common/PageBreadcrumb';
export const notificationsBreadcrumbItems: PageBreadcrumbItem[] = [
  {
    label: 'Pages',
    url: '#!'
  },
  {
    label: 'Notifications',
    active: true
  }
];

export interface Notification {
  id: number | string;
  avatar?: string;
  name: string;
  detail?: string;
  interaction: string;
  interactionIcon: string;
  ago: string;
  icon: string;
  time: string;
  date: string;
  read: boolean;
  // avatarPlaceholder?: boolean;
}

export const notifications: Notification[] = [
  {
    id: '1',
    avatar: team30,
    name: 'г Москва, ул Маршала Полубоярова, д 14, кв 65',
    interactionIcon: '',
    interaction: 'Приступили.',
    detail: ' ',
    ago: '10m',
    icon: 'clock',
    time: '10:41 ',
    date: '01.01.2025',
    read: true
  },
  {
    id: '2',
    avatar: team30,
    name: 'г Москва, ул Маршала Полубоярова, д 14, кв 65',
    interactionIcon: '',
    interaction: 'Приступили.',
    detail: ' ',
    ago: '10m',
    icon: 'clock',
    time: '10:41 ',
    date: '01.01.2025',
    read: true
  },
  {
    id: '3',
    avatar: team30,
    name: 'г Москва, ул Маршала Полубоярова, д 14, кв 65',
    interactionIcon: '',
    interaction: 'Приступили.',
    detail: ' ',
    ago: '10m',
    icon: 'clock',
    time: '10:41 ',
    date: '01.01.2025',
    read: true
  },
  {
    id: '4',
    avatar: team30,
    name: 'г Москва, ул Маршала Полубоярова, д 14, кв 65',
    interactionIcon: '',
    interaction: 'Приступили.',
    detail: ' ',
    ago: '10m',
    icon: 'clock',
    time: '10:41 ',
    date: '01.01.2025',
    read: true
  },
  {
    id: '5',
    avatar: team30,
    name: 'г Москва, ул Маршала Полубоярова, д 14, кв 65',
    interactionIcon: '',
    interaction: 'Приступили.',
    detail: ' ',
    ago: '10m',
    icon: 'clock',
    time: '10:41 ',
    date: '01.01.2025',
    read: true
  },
  {
    id: '6',
    avatar: team30,
    name: 'г Москва, ул Маршала Полубоярова, д 14, кв 65',
    interactionIcon: '',
    interaction: 'Приступили.',
    detail: ' ',
    ago: '10m',
    icon: 'clock',
    time: '10:41 ',
    date: '01.01.2025',
    read: true
  },
  {
    id: '7',
    avatar: team30,
    name: 'г Москва, ул Маршала Полубоярова, д 14, кв 65',
    interactionIcon: '',
    interaction: 'Приступили.',
    detail: ' ',
    ago: '10m',
    icon: 'clock',
    time: '10:41 ',
    date: '01.01.2025',
    read: false
  },
  {
    id: 8,
    avatar: team30,
    name: 'г Москва, ул Маршала Полубоярова, д 14, кв 65',
    interactionIcon: '',
    interaction: 'Приступили.',
    detail: ' ',
    ago: '10m',
    icon: 'clock',
    time: '10:41 ',
    date: '01.01.2025',
    read: true
  }
];
