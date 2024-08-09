import axios from 'axios';
import { handleError } from './ErrorHandler';

const api = 'https://partner.reset-med.ru/api/partner/report/';

export const reportCalAmountOfRewardForTheYear = async () => {
  try {
    return await axios.get(`${api}call/amount-of-reward-for-the-year`);
  } catch (error) {
    handleError(error);
  }
};
