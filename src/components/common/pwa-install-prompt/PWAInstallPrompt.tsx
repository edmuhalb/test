import { useState, useEffect, useCallback } from 'react';

interface BeforeInstallPromptEvent extends Event {
  prompt(): Promise<void>;
  userChoice: Promise<{ outcome: 'accepted' | 'dismissed' }>;
}

/**
 * Detects if app is already running in standalone (installed) mode.
 */
const isStandalone = (): boolean =>
  window.matchMedia('(display-mode: standalone)').matches ||
  (navigator as any).standalone === true;

/**
 * Full-screen PWA install prompt.
 * Shown when the app is opened in a browser (not installed) and the
 * `beforeinstallprompt` event fires (Chromium) or on iOS Safari.
 */
const PWAInstallPrompt = () => {
  const [deferredPrompt, setDeferredPrompt] =
    useState<BeforeInstallPromptEvent | null>(null);
  const [showPrompt, setShowPrompt] = useState(false);
  const [isIOS, setIsIOS] = useState(false);

  useEffect(() => {
    // Already installed — nothing to show
    if (isStandalone()) return;

    // iOS detection (Safari doesn't fire beforeinstallprompt)
    const ua = navigator.userAgent;
    const isiOS =
      /iPad|iPhone|iPod/.test(ua) ||
      (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);
    const isSafari = /Safari/.test(ua) && !/CriOS|FxiOS|Chrome/.test(ua);

    if (isiOS && isSafari) {
      setIsIOS(true);
      setShowPrompt(true);
      return;
    }

    // Chromium-based browsers
    const handler = (e: Event) => {
      e.preventDefault();
      setDeferredPrompt(e as BeforeInstallPromptEvent);
      setShowPrompt(true);
    };

    window.addEventListener('beforeinstallprompt', handler);
    return () => window.removeEventListener('beforeinstallprompt', handler);
  }, []);

  const handleInstall = useCallback(async () => {
    if (!deferredPrompt) return;
    deferredPrompt.prompt();
    const { outcome } = await deferredPrompt.userChoice;
    if (outcome === 'accepted') {
      setShowPrompt(false);
    }
    setDeferredPrompt(null);
  }, [deferredPrompt]);

  if (!showPrompt) return null;

  return (
    <div className="pwa-install-overlay">
      <div className="pwa-install-card">
        <div className="pwa-install-icon">
          <img src="/logo192.png" alt="Ресет" width={72} height={72} />
        </div>
        <h2 className="pwa-install-title">Установите приложение</h2>
        <p className="pwa-install-desc">
          Добавьте «Ресет» на главный экран для быстрого доступа
        </p>

        {isIOS ? (
          <div className="pwa-install-ios">
            <p>
              Нажмите{' '}
              <strong>
                <svg
                  width="20"
                  height="20"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  strokeWidth="2"
                  strokeLinecap="round"
                  strokeLinejoin="round"
                  style={{ verticalAlign: 'middle' }}
                >
                  <path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8" />
                  <polyline points="16 6 12 2 8 6" />
                  <line x1="12" y1="2" x2="12" y2="15" />
                </svg>{' '}
                Поделиться
              </strong>{' '}
              и выберите{' '}
              <strong>«На экран "Домой"»</strong>
            </p>
          </div>
        ) : (
          <button
            type="button"
            className="pwa-install-btn"
            onClick={handleInstall}
          >
            Установить
          </button>
        )}
      </div>
    </div>
  );
};

export default PWAInstallPrompt;
