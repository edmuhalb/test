import { useAuth } from 'context/useAuth';
import { useState } from 'react';
import { Card } from 'react-bootstrap';
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
    setActiveChat((prev) => (prev === chat ? null : chat));
  };

  return (
    <>
      <div className="support-chat-buttons d-flex flex-column gap-2">
        <Card
          className="support-chat-btn border shadow-sm cursor-pointer"
          onClick={() => handleToggle('kc')}
          role="button"
          style={{ minWidth: '8rem' }}
        >
          <Card.Body className="py-2 px-3 d-flex align-items-center justify-content-center">
            <span
              className={`small fw-semibold ${
                activeChat === 'kc' ? 'text-primary' : 'text-body'
              }`}
            >
              Колцентр
            </span>
          </Card.Body>
        </Card>
        <Card
          className="support-chat-btn border shadow-sm cursor-pointer"
          onClick={() => handleToggle('buh')}
          role="button"
          style={{ minWidth: '8rem' }}
        >
          <Card.Body className="py-2 px-3 d-flex align-items-center justify-content-center">
            <span
              className={`small fw-semibold ${
                activeChat === 'buh' ? 'text-primary' : 'text-body'
              }`}
            >
              Бухгалтерия
            </span>
          </Card.Body>
        </Card>
      </div>

      {activeChat && (
        <OneCDialogChat
          key={activeChat}
          integrationToken={TOKENS[activeChat]}
          partnerId={user?.id}
          partnerName={user?.name}
        />
      )}
    </>
  );
};

export default SupportChatButtons;
