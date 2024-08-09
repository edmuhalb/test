import axios from 'axios';
import { handleError } from './ErrorHandler';
import { UserProfileToken } from '../models/User';

const api = 'https://partner.reset-med.ru/api/partner/';

export const loginAPI = async (phone: string, password: string) => {
  try {
    return await axios.post<UserProfileToken>(`${api}login_check`, {
      phone,
      password
    });
  } catch (error) {
    handleError(error);
  }
};
