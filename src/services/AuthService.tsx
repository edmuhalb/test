import axios from 'axios';
import { handleError } from './ErrorHandler';
import { UserProfileToken } from '../models/User';
import { BASE_URL } from '../config';

const api = `${BASE_URL}`;

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

export const forgotPasswordAPI = async (phone: string) => {
  try {
    return await axios.post(`${api}forgot-password`, { phone });
  } catch (error) {
    handleError(error);
  }
};

export const checkCallStatusAPI = async (check_id: string) => {
  try {
    return await axios.post(`${api}check-call-status`, { check_id });
  } catch (error) {
    handleError(error);
  }
};

export const resetPasswordAPI = async (check_id: string, password: string) => {
  try {
    return await axios.post(`${api}reset-password`, { check_id, password });
  } catch (error) {
    handleError(error);
  }
};
