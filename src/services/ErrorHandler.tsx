import { isAxiosError } from 'axios';

let modalRoot: HTMLDivElement | null = null;

const showErrorModal = (message: string) => {
  // Убираем предыдущее модальное окно если есть
  if (modalRoot) {
    modalRoot.remove();
  }

  modalRoot = document.createElement('div');
  modalRoot.innerHTML = `
    <div class="error-modal-overlay">
      <div class="error-modal-card">
        <div class="error-modal-icon">!</div>
        <div class="error-modal-message">${message}</div>
        <button class="error-modal-btn" type="button">Понятно</button>
      </div>
    </div>
  `;

  document.body.appendChild(modalRoot);

  const btn = modalRoot.querySelector('.error-modal-btn');
  const overlay = modalRoot.querySelector('.error-modal-overlay');

  const close = () => {
    if (modalRoot) {
      modalRoot.remove();
      modalRoot = null;
    }
  };

  btn?.addEventListener('click', close);
  overlay?.addEventListener('click', (e) => {
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
