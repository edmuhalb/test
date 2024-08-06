import { PageBreadcrumbItem } from 'components/common/PageBreadcrumb';
import team30 from 'assets/img/team/40x40/avatar.webp';
import team57 from 'assets/img/team/40x40/avatar.webp';
import team59 from 'assets/img/team/40x40/avatar.webp';
import team58 from 'assets/img/team/40x40/avatar.webp';
import team60 from 'assets/img/team/40x40/avatar.webp';
import team34 from 'assets/img/team/40x40/avatar.webp';
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
    name: 'Jessie Samson',
    interactionIcon: '💬',
    interaction: 'Mentioned you in a comment.',
    detail: ' "Well done! Proud of you ❤️ " ',
    ago: '10m',
    icon: 'clock',
    time: '10:41 AM ',
    date: 'August 7,2021',
    read: true
  },
  {
    id: '2',
    avatar: team58,
    name: 'Jane Foster',
    interactionIcon: '📅',
    interaction: 'Created an event.',
    detail: 'Rome holidays',
    ago: '20m',
    icon: 'clock',
    time: '10:20 AM ',
    date: 'August 7,2021',
    read: false
  },
  {
    id: '3',
    name: 'Jessie Samson',
    interactionIcon: '👍',
    interaction: 'Liked your comment.',
    detail: '"Amazing Works️"',
    ago: '1h',
    icon: 'clock',
    time: '9:30 AM ',
    date: 'August 7,2021',
    read: false
  },
  {
    id: '4',
    avatar: team57,
    name: 'Kiera Anderson',
    interactionIcon: '💬',
    interaction: 'Mentioned you in a comment.',
    detail: '"This is too good to be true!"',
    ago: '',
    icon: 'fas fa-clock',
    time: '9:11 AM ',
    date: 'August 7,2021',
    read: false
  },
  {
    id: '5',
    avatar: team59,
    name: 'Herman Carter',
    interactionIcon: '👤',
    interaction: 'Tagged you in a comment',
    detail: 'post',
    ago: '',
    icon: 'fas fa-clock',
    time: '10:58 PM ',
    date: 'August 7,2021',
    read: false
  },
  {
    id: '6',
    avatar: team58,
    name: 'Benjamin Button',
    interactionIcon: '👍',
    interaction: 'Liked your comment.',
    detail: 'Welcome to the team️',
    ago: '',
    icon: 'fas fa-clock',
    time: '10:18 AM ',
    date: 'August 7,2021',
    read: true
  },
  {
    id: '7',
    avatar: team60,
    name: 'Aron Paul',
    interactionIcon: '📷',
    interaction: 'Tagged you in a photo',
    detail: 'photo',
    ago: '',
    icon: 'fas fa-clock',
    time: '9:53 AM ',
    date: 'August 7,2021',
    read: true
  },
  {
    id: 8,
    avatar: team34,
    name: 'Rick Sanchez',
    interactionIcon: '💬',
    interaction: 'Mentioned you in a comment',
    detail: '"You need to see these amazing photos️"',
    ago: '',
    icon: 'fas fa-clock',
    time: '9:45 AM ',
    date: 'August 7,2021',
    read: true
  }
];
