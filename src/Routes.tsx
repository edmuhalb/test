import MainLayout from 'layouts/MainLayout';
import { RouteObject, createBrowserRouter } from 'react-router-dom';

import MainLayoutProvider from 'providers/MainLayoutProvider';
import Error404 from 'pages/error/Error404';
import App from 'App';
import CardSignIn from 'pages/SignIn';
import { ProtectedRoute } from './ProtectedRoute';
import Dashboard from 'pages/Dashboard';
import Profile from 'pages/Profile';
import Calls from 'pages/calls/Calls';
import CallDetails from './pages/calls/CallDetails';
import CallCreate from './pages/calls/CallCreate';
import Hospitals from './pages/hospital/Hospitals';

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
                <Dashboard />
              </ProtectedRoute>
            )
          },
          {
            path: 'profile',
            element: (
              <ProtectedRoute>
                <Profile />
              </ProtectedRoute>
            )
          },

          {
            path: '/calls',
            children: [
              {
                index: true,
                element: (
                  <ProtectedRoute>
                    <Calls />
                  </ProtectedRoute>
                )
              },
              {
                path: `/calls/create`,
                element: (
                  <ProtectedRoute>
                    <CallCreate />
                  </ProtectedRoute>
                )
              },
              {
                path: `/calls/:callId`,
                element: (
                  <ProtectedRoute>
                    <CallDetails />
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
                    <Hospitals />
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
