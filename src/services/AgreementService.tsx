import axios from 'axios';
import { handleError } from './ErrorHandler';
import { BASE_URL } from '../config';

const api = `${BASE_URL}agreement`;

export const agreementGetAPI = async () => {
  try {
    return await axios.get(api);
  } catch (error) {
    handleError(error);
  }
};
