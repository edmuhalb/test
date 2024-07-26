import MainLayout from 'layouts/MainLayout';
import { RouteObject, createBrowserRouter } from 'react-router-dom';
import Ecommerce from 'pages/dashboard/ecommerce';
import AddProduct from 'pages/apps/e-commerce/admin/AddProduct';
import Orders from 'pages/apps/e-commerce/admin/Orders';
import OrderDetails from 'pages/apps/e-commerce/admin/OrderDetails';
import CustomerDetails from 'pages/apps/e-commerce/admin/CustomerDetails';
import MainLayoutProvider from 'providers/MainLayoutProvider';
import Error404 from 'pages/error/Error404';
import Error403 from 'pages/error/Error403';
import Error500 from 'pages/error/Error500';
import App from 'App';
import CardSignIn from 'pages/pages/authentication/card/SignIn';

const routes: RouteObject[] = [
  {
    element: <App />,
    children: [
      {
        path: '/',
        element: (
          <MainLayoutProvider>
            <MainLayout />
          </MainLayoutProvider>
        ),
        children: [
          {
            index: true,
            element: <Ecommerce />
          },
          {
            path: '/apps',
            children: [
              {
                path: 'e-commerce/admin',
                children: [
                  {
                    path: 'add-product',
                    element: <AddProduct />
                  },
                  {
                    path: 'orders',
                    element: <Orders />
                  },
                  {
                    path: 'order-details',
                    element: <OrderDetails />
                  },
                  {
                    path: 'customer-details',
                    element: <CustomerDetails />
                  }
                ]
              }
            ]
          }
        ]
      },

      {
        path: '/pages/errors/',
        children: [
          {
            path: '404',
            element: <Error404 />
          },
          {
            path: '403',
            element: <Error403 />
          },
          {
            path: '500',
            element: <Error500 />
          }
        ]
      },
      {
        path: '/pages/authentication/card/',
        children: [
          {
            path: 'sign-in',
            element: <CardSignIn />
          }
        ]
      },
      {
        path: '*',
        element: <Error404 />
      }
    ]
  }
];

export const router = createBrowserRouter(routes);

export default routes;
