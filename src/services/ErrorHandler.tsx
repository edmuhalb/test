import { isAxiosError } from 'axios';
import { toast } from 'react-toastify';

export const handleError = (error: any) => {
  if (isAxiosError(error)) {
    const err = error.response;

    if (!err) {
      toast.error('Нет соединения с сервером');
      return;
    }

    if (err.status === 404) {
      toast.error('Сервис временно недоступен');
      return;
    }

    if (err.status === 429) {
      toast.error(
        err.data?.error || 'Слишком много запросов, подождите немного'
      );
      return;
    }

    if (err.status === 401) {
      if (err.data?.message === 'Invalid credentials.') {
        toast.error('Неверный логин или пароль');
      } else {
        toast.error('Сессия истекла, войдите заново');
        window.history.pushState({}, 'LoginPage', '/login');
      }
      return;
    }

    // Ошибки валидации и бизнес-логики (400, 422, etc.)
    if (err.data?.error) {
      toast.error(err.data.error);
      return;
    }

    if (err.data?.message) {
      toast.error(err.data.message);
      return;
    }

    if (Array.isArray(err.data?.errors)) {
      for (const val of err.data.errors) {
        toast.error(val.description || val.message || String(val));
      }
      return;
    }

    // Fallback
    toast.error('Произошла ошибка, попробуйте позже');
  } else {
    toast.error('Произошла ошибка, попробуйте позже');
  }
};
