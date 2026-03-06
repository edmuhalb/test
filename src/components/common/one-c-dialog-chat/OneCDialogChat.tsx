import { useAuth } from 'context/useAuth';
import { useEffect, useRef } from 'react';

const SCRIPT_BASE_URL =
  'https://integrations.1cdialog.com/integration/webchat/';
const SCRIPT_ID = 'one-c-dialog-chat-script';
/** Домены iframe'ов виджета: скрипт на 1cdialog.com, фреймы на webchat.dialog.online */
const WIDGET_IFRAME_DOMAINS = ['1cdialog.com', 'dialog.online'];
const POLL_INTERVAL_MS = 150;
const POLL_TIMEOUT_MS = 20000;
/** Задержка перед open(), чтобы iframe виджета успел загрузиться и принять setContactInfo */
const OPEN_DELAY_MS = 200;

/** Удаляет из DOM скрипт и все элементы виджета (iframe'ы), чтобы при смене чата не копились фреймы */
function removeWidgetDom(): void {
  try {
    window.CollaborationSystemWebChat1CE?.close();
  } catch {
    // ignore
  }
  WIDGET_IFRAME_DOMAINS.forEach(domain => {
    document
      .querySelectorAll<HTMLIFrameElement>(`iframe[src*="${domain}"]`)
      .forEach(el => el.remove());
  });
  const scriptEl = document.getElementById(SCRIPT_ID);
  if (scriptEl?.parentNode) {
    scriptEl.parentNode.removeChild(scriptEl);
  }
}

export type ContactInfo = {
  /** id партнёра */
  name?: string;
  /** название партнёра */
  fullName?: string;
  email?: string;
  phone?: string;
};

type Props = {
  /** Токен интеграции 1С:Диалог (например: 917338:T27KBnmZ6nOZ01txWLCM99PpxMlV2W77) */
  integrationToken: string;
  /** id партнёра (если не передан — берётся из user.id) */
  partnerId?: string;
  /** название партнёра (если не передан — берётся из user.name) */
  partnerName?: string;
};

/**
 * Подключает виджет чата 1С:Диалог через внешний скрипт.
 * Чат отображается только для авторизованных пользователей.
 * После загрузки устанавливает контактные данные и автоматически открывает окно чата.
 */
const OneCDialogChat = ({
  integrationToken,
  partnerId,
  partnerName
}: Props) => {
  const { isLoggedIn, user } = useAuth();
  const isAuthorized = isLoggedIn();
  const openedRef = useRef(false);

  const name = partnerId ?? user?.id ?? '';
  const fullName = partnerName ?? user?.name ?? '';

  useEffect(() => {
    console.log('useEffect', { integrationToken, isAuthorized });
    if (!integrationToken?.trim() || !isAuthorized) {
      return;
    }

    if (document.getElementById(SCRIPT_ID)) {
      return;
    }

    const script = document.createElement('script');
    script.id = SCRIPT_ID;
    // Токен в пути как есть (без encodeURIComponent), иначе сервер может не распознать 917338:T27...
    script.src = `${SCRIPT_BASE_URL}${integrationToken.trim()}`;
    script.async = true;

    let openTimeoutId: ReturnType<typeof setTimeout> | null = null;

    const tryInitChat = () => {
      const chat = window.CollaborationSystemWebChat1CE;
      if (!chat || openedRef.current) return true;
      try {
        chat.setContactInfo({ name, fullName });
        // Задержка перед open(): iframe виджета должен успеть загрузиться и принять setContactInfo
        openTimeoutId = setTimeout(() => {
          try {
            if (!openedRef.current) {
              chat.open();
              openedRef.current = true;
            }
          } catch {
            // ignore
          }
          openTimeoutId = null;
        }, OPEN_DELAY_MS);
        return true;
      } catch {
        return false;
      }
    };

    const pollStart = Date.now();
    const pollId = setInterval(() => {
      if (tryInitChat() || Date.now() - pollStart > POLL_TIMEOUT_MS) {
        clearInterval(pollId);
      }
    }, POLL_INTERVAL_MS);

    script.onload = () => {
      tryInitChat();
    };

    document.body.appendChild(script);

    return () => {
      clearInterval(pollId);
      if (openTimeoutId !== null) clearTimeout(openTimeoutId);
      openedRef.current = false;
      removeWidgetDom();
    };
  }, [integrationToken, isAuthorized, name, fullName]);

  return null;
};

export default OneCDialogChat;
