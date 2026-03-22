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
import { PWAInstallPrompt } from 'components/common/pwa-install-prompt';

// Register service worker for PWA
if ('serviceWorker' in navigator) {
  window.addEventListener('load', () => {
    navigator.serviceWorker.register('/sw.js').catch(() => {
      // SW registration failed — app still works without it
    });
  });
}

const root = ReactDOM.createRoot(
  document.getElementById('root') as HTMLElement
);
root.render(
  <React.StrictMode>
    <AppProvider>
      <SettingsPanelProvider>
        <BreakpointsProvider>
          <ChatWidgetProvider>
            <PWAInstallPrompt />
            <RouterProvider router={router} />
          </ChatWidgetProvider>
        </BreakpointsProvider>
        <ToastContainer />
      </SettingsPanelProvider>
    </AppProvider>
  </React.StrictMode>
);
