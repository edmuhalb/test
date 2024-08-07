import axios from 'axios';
import { handleError } from './ErrorHandler';

const api = 'http://ambulance.beget.tech/api/partner/calls';

export const callCreateAPI = async (fields: string) => {
  try {
    return await axios.post(api, fields);
  } catch (error) {
    handleError(error);
  }
};

export const callsIndexAPI = async (params?: any) => {
  try {
    return await axios.get(api, { params });
  } catch (error) {
    handleError(error);
  }
};

export const callsGetAPI = async (id: any) => {
  try {
    return await axios.get(`${api}/${id}`);
  } catch (error) {
    handleError(error);
  }
};
