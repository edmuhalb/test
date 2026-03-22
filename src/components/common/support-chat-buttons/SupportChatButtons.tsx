import { useAuth } from 'context/useAuth';
import { useState, useRef, useEffect } from 'react';
import classNames from 'classnames';
import FeatherIcon from 'feather-icons-react';
import { OneCDialogChat } from '../one-c-dialog-chat';

const TOKENS = {
  kc: '917338:T27KBnmZ6nOZ01txWLCM99PpxMlV2W77',
  buh: '509794:MgnrfTwbXsqasmICx3EEuY4e1ghRUw6N'
} as const;

const CHAT_LABELS: Record<keyof typeof TOKENS, string> = {
  kc: 'Контактный центр',
  buh: 'Бухгалтерия'
};

const CHAT_ICONS: Record<keyof typeof TOKENS, string> = {
  kc: 'phone',
  buh: 'file-text'
};

type ActiveChat = keyof typeof TOKENS | null;

const SupportChatButtons = () => {
  const { user, isLoggedIn } = useAuth();
  const [activeChat, setActiveChat] = useState<ActiveChat>(null);
  const [menuOpen, setMenuOpen] = useState(false);
  const menuRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    const handleClickOutside = (e: MouseEvent) => {
      if (menuRef.current && !menuRef.current.contains(e.target as Node)) {
        setMenuOpen(false);
      }
    };
    if (menuOpen) {
      document.addEventListener('mousedown', handleClickOutside);
    }
    return () => document.removeEventListener('mousedown', handleClickOutside);
  }, [menuOpen]);

  if (!isLoggedIn()) {
    return null;
  }

  const handleSelect = (chat: keyof typeof TOKENS) => {
    setMenuOpen(false);
    setActiveChat(prev => (prev === chat ? null : chat));
  };

  // When chat is open — show only a small close button so it doesn't overlap the chat
  if (activeChat) {
    return (
      <>
        <button
          type="button"
          className="support-fab support-fab--close"
          onClick={() => setActiveChat(null)}
          aria-label="Закрыть чат"
        >
          <FeatherIcon icon="x" size={20} />
        </button>
        <OneCDialogChat
          key={activeChat}
          integrationToken={TOKENS[activeChat]}
          partnerId={user?.partner_id}
          partnerName={user?.partner_name}
        />
      </>
    );
  }

  return (
    <div ref={menuRef} style={{ position: 'relative' }}>
      {menuOpen && (
        <div className="support-chat-menu">
          {(Object.keys(TOKENS) as Array<keyof typeof TOKENS>).map(key => (
            <button
              key={key}
              type="button"
              className="support-chat-menu__item"
              onClick={() => handleSelect(key)}
            >
              <FeatherIcon icon={CHAT_ICONS[key]} size={18} />
              <span>{CHAT_LABELS[key]}</span>
            </button>
          ))}
        </div>
      )}
      <button
        type="button"
        className="support-fab"
        onClick={() => setMenuOpen(prev => !prev)}
        aria-label="Связаться"
      >
        <FeatherIcon icon="message-circle" size={22} />
      </button>
    </div>
  );
};

export default SupportChatButtons;
