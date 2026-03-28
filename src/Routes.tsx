import MainLayout from 'layouts/MainLayout';
import { RouteObject, createBrowserRouter } from 'react-router-dom';

import MainLayoutProvider from 'providers/MainLayoutProvider';
import Error404 from 'pages/error/Error404';
import App from 'App';
import { ProtectedRoute } from './ProtectedRoute';
import CallsListPage from './pages/calls/list';
import HospitalListPage from './pages/hospital';
import LoginPage from './pages/login';
import ForgotPasswordPage from './pages/forgot-password';
import ResetPasswordPage from './pages/reset-password';
import NotificationsPage from 'pages/notifications';
import CallsCreatePage from 'pages/calls/create';
import AgreementPage from 'pages/agreement';

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
                <CallsListPage />
              </ProtectedRoute>
            )
          },
          //{
          //  index: true,
          //  element: (
          //    <ProtectedRoute>
          //      <DashboardPage />
          //    </ProtectedRoute>
          //  )
          //},
          {
            path: '/calls',
            children: [
              {
                index: true,
                element: (
                  <ProtectedRoute>
                    <CallsListPage />
                  </ProtectedRoute>
                )
              },
              {
                path: `/calls/create`,
                element: (
                  <ProtectedRoute>
                    <CallsCreatePage />
                  </ProtectedRoute>
                )
              }
            ]
          },
          {
            path: '/hospitals',
            children: [
              {
                index: true,
                element: (
                  <ProtectedRoute>
                    <HospitalListPage />
                  </ProtectedRoute>
                )
              }
            ]
          },
          // {
          //   path: '/results',
          //   children: [
          //     {
          //       index: true,
          //       element: (
          //         <ProtectedRoute>
          //           <ResultsPage />
          //         </ProtectedRoute>
          //       )
          //     }
          //   ]
          // },
          {
            path: '/notifications',
            children: [
              {
                index: true,
                element: (
                  <ProtectedRoute>
                    <NotificationsPage />
                  </ProtectedRoute>
                )
              }
            ]
          },
          {
            path: '/agreement',
            children: [
              {
                index: true,
                element: (
                  <ProtectedRoute>
                    <AgreementPage />
                  </ProtectedRoute>
                )
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
            element: <LoginPage />
          },
          {
            path: 'forgot-password',
            element: <ForgotPasswordPage />
          },
          {
            path: 'reset-password',
            element: <ResetPasswordPage />
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
