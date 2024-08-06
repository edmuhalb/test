import MainLayout from 'layouts/MainLayout';
import { RouteObject, createBrowserRouter } from 'react-router-dom';
import Ecommerce from 'pages/dashboard/ecommerce';
import AddProduct from 'pages/apps/e-commerce/admin/AddProduct';
import Orders from 'pages/apps/e-commerce/admin/Orders';
import OrderDetails from 'pages/apps/e-commerce/admin/OrderDetails';
import CustomerDetails from 'pages/apps/e-commerce/admin/CustomerDetails';
import MainLayoutProvider from 'providers/MainLayoutProvider';
import Error404 from 'pages/error/Error404';
import App from 'App';
import CardSignIn from 'pages/SignIn';
import { ProtectedRoute } from './ProtectedRoute';

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
            element: (
              <ProtectedRoute>
                <Ecommerce />
              </ProtectedRoute>
            )
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
        path: '/',
        children: [
          {
            path: 'login',
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
