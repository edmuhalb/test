import { useAuth } from 'context/useAuth';
import { useState } from 'react';
import classNames from 'classnames';
import Button from 'components/base/Button';
import { OneCDialogChat } from '../one-c-dialog-chat';

const TOKENS = {
  kc: '917338:T27KBnmZ6nOZ01txWLCM99PpxMlV2W77',
  buh: '509794:MgnrfTwbXsqasmICx3EEuY4e1ghRUw6N'
} as const;

type ActiveChat = keyof typeof TOKENS | null;

const SupportChatButtons = () => {
  const { user, isLoggedIn } = useAuth();
  const [activeChat, setActiveChat] = useState<ActiveChat>(null);

  if (!isLoggedIn()) {
    return null;
  }

  const handleToggle = (chat: keyof typeof TOKENS) => {
    setActiveChat(prev => (prev === chat ? null : chat));
  };

  return (
    <>
      <div className="support-chat-buttons d-flex flex-row gap-2">
        <Button
          style={{ width: '14rem' }}
          className={classNames('border border-primary bg-white')}
          onClick={() => handleToggle('kc')}
        >
          <span
            className={classNames(
              'fs-8 btn-text text-nowrap fw-semibold',
              activeChat === 'kc' ? 'text-primary' : 'text-body'
            )}
          >
            Колцентр
          </span>
        </Button>
        <Button
          style={{ width: '14rem' }}
          className={classNames('border border-primary bg-white')}
          onClick={() => handleToggle('buh')}
        >
          <span
            className={classNames(
              'fs-8 btn-text text-nowrap fw-semibold',
              activeChat === 'buh' ? 'text-primary' : 'text-body'
            )}
          >
            Бухгалтерия
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
