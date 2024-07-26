import { IconProp } from '@fortawesome/fontawesome-svg-core';
import {
  Icon,
  UilChartPie,
  UilCube,
  UilFilesLandscapesAlt
} from '@iconscout/react-unicons';

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
        name: 'home',
        icon: 'pie-chart',
        flat: true,
        hasNew: true,
        pages: [
          {
            name: 'e-commerce',
            path: '/',
            pathName: 'default-dashboard',
            topNavIcon: 'shopping-cart',
            active: true
          }
        ]
      }
    ]
  },
  {
    label: 'apps',
    icon: UilCube,
    pages: [
      {
        name: 'add-product',
        path: '/apps/e-commerce/admin/add-product',
        pathName: 'e-commerce-add-product',
        icon: 'shopping-cart',
        active: true
      },
      {
        name: 'customer-details',
        path: '/apps/e-commerce/admin/customer-details',
        pathName: 'e-commerce-customer-details',
        icon: 'shopping-cart',
        active: true
      },
      {
        name: 'orders',
        path: '/apps/e-commerce/admin/orders',
        pathName: 'e-commerce-orders',
        icon: 'shopping-cart',
        active: true
      },
      {
        name: 'order-details',
        path: '/apps/e-commerce/admin/order-details',
        pathName: 'e-commerce-order-details',
        icon: 'shopping-cart',
        active: true
      }
    ]
  },
  {
    label: 'pages',
    icon: UilFilesLandscapesAlt,
    pages: [
      {
        name: 'sign-in',
        path: 'pages/authentication/card/sign-in',
        pathName: 'card-signin',
        active: true
      }
    ]
  }
];
