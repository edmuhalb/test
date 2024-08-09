import axios from 'axios';
import { handleError } from './ErrorHandler';

const api = 'https://partner.reset-med.ru/api/partner/hospitals';

export const hospitalsIndexAPI = async (params?: any) => {
  try {
    return await axios.get(api, { params });
  } catch (error) {
    handleError(error);
  }
};
