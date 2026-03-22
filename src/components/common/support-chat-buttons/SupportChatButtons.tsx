import { useAuth } from 'context/useAuth';
import { useState, useRef, useEffect } from 'react';
import classNames from 'classnames';
import Button from 'components/base/Button';
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

  return (
    <>
      <div ref={menuRef} style={{ position: 'relative' }}>
        {menuOpen && (
          <div className="support-chat-menu">
            {(Object.keys(TOKENS) as Array<keyof typeof TOKENS>).map(key => (
              <button
                key={key}
                type="button"
                className={classNames('support-chat-menu__item', {
                  'support-chat-menu__item--active': activeChat === key
                })}
                onClick={() => handleSelect(key)}
              >
                <FeatherIcon icon={CHAT_ICONS[key]} size={16} />
                <span>{CHAT_LABELS[key]}</span>
              </button>
            ))}
          </div>
        )}
        <Button
          className={classNames('border border-primary bg-white')}
          style={{ width: '14rem' }}
          onClick={() => setMenuOpen(prev => !prev)}
        >
          <FeatherIcon
            icon="message-circle"
            size={16}
            className={classNames(
              'me-2',
              activeChat ? 'text-primary' : 'text-body'
            )}
          />
          <span
            className={classNames(
              'fs-8 btn-text text-nowrap fw-semibold',
              activeChat ? 'text-primary' : 'text-body'
            )}
          >
            {activeChat ? CHAT_LABELS[activeChat] : 'Связаться'}
          </span>
        </Button>
      </div>

      {activeChat && (
        <OneCDialogChat
          key={activeChat}
          integrationToken={TOKENS[activeChat]}
          partnerId={user?.partner_id}
          partnerName={user?.partner_name}
        />
      )}
    </>
  );
};

export default SupportChatButtons;
