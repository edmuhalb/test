import { IconProp } from '@fortawesome/fontawesome-svg-core';
import { Icon, UilChartPie } from '@iconscout/react-unicons';

export interface Route {
  name: string;
  icon?: IconProp | string | string[];
  iconSet?: 'font-awesome' | 'feather' | 'unicons';
  pages?: Route[];
  path?: string;
  pathName?: string;
  flat?: boolean;
  topNavIcon?: string;
  dropdownInside?: boolean;
  active?: boolean;
  new?: boolean;
  hasNew?: boolean;
  next?: boolean;
}

export interface RouteItems {
  label: string;
  horizontalNavLabel?: string;
  icon: Icon;
  labelDisabled?: boolean;
  pages: Route[];
  megaMenu?: boolean;
  active?: boolean;
}

export const routes: RouteItems[] = [
  {
    label: 'dashboard',
    horizontalNavLabel: 'home',
    active: true,
    icon: UilChartPie,
    labelDisabled: true,
    pages: [
      {
        icon: 'pie-chart',
        flat: true,
        hasNew: false,
        name: 'Рабочий стол',
        path: '/',
        pathName: 'default-dashboard',
        topNavIcon: 'shopping-cart',
        active: true
      },
      {
        icon: 'pie-chart',
        flat: true,
        hasNew: false,
        name: 'Вызовы',
        path: '/calls',
        pathName: 'default-dashboard',
        topNavIcon: 'shopping-cart',
        active: true
      },
      {
        icon: 'pie-chart',
        flat: true,
        hasNew: false,
        name: 'Стационар',
        path: '/hospitals',
        pathName: 'default-dashboard',
        topNavIcon: 'shopping-cart',
        active: true
      },
      {
        icon: 'pie-chart',
        flat: true,
        hasNew: false,
        name: 'Итоги',
        path: '/results',
        pathName: 'default-dashboard',
        topNavIcon: 'shopping-cart',
        active: true
      }
    ]
  }
];
