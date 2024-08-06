import { isAxiosError } from 'axios';

export const handleError = (error: any) => {
  if (isAxiosError(error)) {
    const err = error.response;
    if (err && Array.isArray(err?.data.errors)) {
      for (const val of err.data.errors) {
        console.log(val.description);
      }
    } else if (typeof err?.data.errors === 'object') {
      for (const e of err.data.errors) {
        console.log(err.data.errors[e]);
      }
    } else if (err?.data) {
      console.log(err.data);
    } else if (err?.status === 401) {
      console.log('Пожалуйста авторизуйтесь');
      window.history.pushState({}, 'LoginPage', '/login');
    } else if (err) {
      console.log(err?.data);
    }
  }
};
