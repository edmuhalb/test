import { isAxiosError } from 'axios';

let modalRoot: HTMLDivElement | null = null;

const showErrorModal = (message: string) => {
  if (modalRoot) {
    modalRoot.remove();
  }

  modalRoot = document.createElement('div');

  const overlay = document.createElement('div');
  Object.assign(overlay.style, {
    position: 'fixed',
    top: '0',
    left: '0',
    right: '0',
    bottom: '0',
    zIndex: '10000',
    display: 'flex',
    alignItems: 'center',
    justifyContent: 'center',
    background: 'rgba(0,0,0,0.5)',
    backdropFilter: 'blur(4px)',
    padding: '1.5rem'
  });

  const card = document.createElement('div');
  Object.assign(card.style, {
    width: '100%',
    maxWidth: '20rem',
    background: '#fff',
    borderRadius: '1rem',
    padding: '2rem 1.5rem',
    textAlign: 'center',
    boxShadow: '0 12px 40px rgba(0,0,0,0.2)'
  });

  // Подхватываем тёмную тему
  if (document.documentElement.getAttribute('data-bs-theme') === 'dark') {
    card.style.background = '#1e2a3a';
    card.style.color = '#d0d6e0';
  }

  const icon = document.createElement('div');
  Object.assign(icon.style, {
    width: '3rem',
    height: '3rem',
    margin: '0 auto 1rem',
    borderRadius: '50%',
    background: '#e63757',
    color: '#fff',
    fontSize: '1.5rem',
    fontWeight: '800',
    lineHeight: '3rem',
    textAlign: 'center'
  });
  icon.textContent = '!';

  const msg = document.createElement('div');
  Object.assign(msg.style, {
    fontSize: '0.9375rem',
    fontWeight: '600',
    marginBottom: '1.5rem',
    lineHeight: '1.5',
    whiteSpace: 'pre-line'
  });
  msg.textContent = message;

  const btn = document.createElement('button');
  Object.assign(btn.style, {
    display: 'block',
    width: '100%',
    padding: '0.75rem',
    border: 'none',
    borderRadius: '0.625rem',
    background: '#3874ff',
    color: '#fff',
    fontSize: '0.9375rem',
    fontWeight: '700',
    cursor: 'pointer'
  });
  btn.type = 'button';
  btn.textContent = 'Понятно';

  card.appendChild(icon);
  card.appendChild(msg);
  card.appendChild(btn);
  overlay.appendChild(card);
  modalRoot.appendChild(overlay);
  document.body.appendChild(modalRoot);

  const close = () => {
    if (modalRoot) {
      modalRoot.remove();
      modalRoot = null;
    }
  };

  btn.addEventListener('click', close);
  overlay.addEventListener('click', e => {
    if (e.target === overlay) close();
  });
};

export const handleError = (error: any) => {
  if (isAxiosError(error)) {
    const err = error.response;

    if (!err) {
      showErrorModal('Нет соединения с сервером');
      return;
    }

    if (err.status === 404) {
      showErrorModal('Сервис временно недоступен');
      return;
    }

    if (err.status === 429) {
      showErrorModal(
        err.data?.error || 'Слишком много запросов, подождите немного'
      );
      return;
    }

    if (err.status === 401) {
      if (err.data?.message === 'Invalid credentials.') {
        showErrorModal('Неверный логин или пароль');
      } else {
        showErrorModal('Сессия истекла, войдите заново');
        window.history.pushState({}, 'LoginPage', '/login');
      }
      return;
    }

    if (err.data?.error) {
      showErrorModal(err.data.error);
      return;
    }

    if (err.data?.message) {
      showErrorModal(err.data.message);
      return;
    }

    if (Array.isArray(err.data?.errors)) {
      const messages = err.data.errors
        .map((val: any) => val.description || val.message || String(val))
        .join('\n');
      showErrorModal(messages);
      return;
    }

    showErrorModal('Произошла ошибка, попробуйте позже');
  } else {
    showErrorModal('Произошла ошибка, попробуйте позже');
  }
};
