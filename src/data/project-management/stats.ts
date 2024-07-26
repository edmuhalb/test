import {
  Icon,
  UilBooks,
  UilInvoice,
  UilRefresh,
  UilUsersAlt
} from '@iconscout/react-unicons';

export interface ProjectManagementStat {
  title: string;
  count: string;
  icon: Icon;
  iconColor: string;
  subtitle: string;
}

export const stats: ProjectManagementStat[] = [
  {
    title: 'Projects',
    count: '32',
    icon: UilBooks,
    iconColor: 'primary',
    subtitle: 'Awating processing'
  },
  {
    title: 'Members',
    icon: UilUsersAlt,
    iconColor: 'success',
    count: '94',
    subtitle: 'Working hard'
  },
  {
    title: 'Invoices',
    icon: UilInvoice,
    iconColor: 'warning',
    count: '23',
    subtitle: 'Soon to be cleared'
  },
  {
    title: 'Refunds',
    icon: UilRefresh,
    iconColor: 'danger',
    count: '3',
    subtitle: 'Fresh start'
  }
];
