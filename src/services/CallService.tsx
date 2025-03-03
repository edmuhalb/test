import axios from 'axios';
import { handleError } from './ErrorHandler';
import { BASE_URL } from '../config';

const api = `${BASE_URL}calls`;

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

export const callsCreateAPI = async (values: any) => {
  return await axios.post(api, values);
};

export const callsGetAPI = async (id: any) => {
  try {
    return await axios.get(`${api}/${id}`);
  } catch (error) {
    handleError(error);
  }
};
