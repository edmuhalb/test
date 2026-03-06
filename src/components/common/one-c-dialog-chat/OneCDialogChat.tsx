import { useAuth } from 'context/useAuth';
import { useEffect, useRef } from 'react';

const SCRIPT_BASE_URL =
  'https://integrations.1cdialog.com/integration/webchat/';
const SCRIPT_ID = 'one-c-dialog-chat-script';
const POLL_INTERVAL_MS = 100;
const POLL_TIMEOUT_MS = 15000;

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
    if (!integrationToken?.trim() || !isAuthorized) {
      return;
    }

    if (document.getElementById(SCRIPT_ID)) {
      return;
    }

    const script = document.createElement('script');
    script.id = SCRIPT_ID;
    script.src = `${SCRIPT_BASE_URL}${encodeURIComponent(
      integrationToken.trim()
    )}`;
    script.async = true;

    const tryInitChat = () => {
      const chat = window.CollaborationSystemWebChat1CE;
      if (!chat || openedRef.current) return true;
      try {
        chat.setContactInfo({ name, fullName });
        chat.open();
        openedRef.current = true;
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
      openedRef.current = false;
      const el = document.getElementById(SCRIPT_ID);
      if (el?.parentNode) {
        el.parentNode.removeChild(el);
      }
    };
  }, [integrationToken, isAuthorized, name, fullName]);

  return null;
};

export default OneCDialogChat;
