import axios from 'axios';
import { handleError } from './ErrorHandler';
import { UserProfileToken } from '../models/User';

const api = 'http://ambulance.beget.tech/api/partner/';

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
