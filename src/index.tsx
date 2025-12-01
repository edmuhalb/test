import AppProvider from 'providers/AppProvider';
import React from 'react';
import ReactDOM from 'react-dom/client';
import BreakpointsProvider from 'providers/BreakpointsProvider';
import SettingsPanelProvider from 'providers/SettingsPanelProvider';
import ChatWidgetProvider from 'providers/ChatWidgetProvider';
import { RouterProvider } from 'react-router-dom';
import { router } from 'Routes';
import { ToastContainer } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';

const root = ReactDOM.createRoot(
  document.getElementById('root') as HTMLElement
);
root.render(
  <React.StrictMode>
    <AppProvider>
      <SettingsPanelProvider>
        <BreakpointsProvider>
          <ChatWidgetProvider>
            <RouterProvider router={router} />
          </ChatWidgetProvider>
        </BreakpointsProvider>
        <ToastContainer />
      </SettingsPanelProvider>
    </AppProvider>
  </React.StrictMode>
);
