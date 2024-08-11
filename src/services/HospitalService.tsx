import axios from 'axios';
import { handleError } from './ErrorHandler';
import { BASE_URL } from '../config';

const api = `${BASE_URL}hospitals/`;

export const hospitalsIndexAPI = async (params?: any) => {
  try {
    return await axios.get(api, { params });
  } catch (error) {
    handleError(error);
  }
};
