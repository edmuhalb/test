import axios from 'axios';
import { handleError } from './ErrorHandler';

const api = 'http://ambulance.beget.tech/api/partner/hospitals';

export const hospitalsIndexAPI = async (params?: any) => {
  try {
    return await axios.get(api, { params });
  } catch (error) {
    handleError(error);
  }
};
