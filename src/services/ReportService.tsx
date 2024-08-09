import axios from 'axios';
import { handleError } from './ErrorHandler';

const api = 'http://ambulance.beget.tech/api/partner/report/';

export const reportCalAmountOfRewardForTheYear = async () => {
  try {
    return await axios.get(`${api}call/amount-of-reward-for-the-year`);
  } catch (error) {
    handleError(error);
  }
};

export const reportCalNumberByStatus = async () => {
  try {
    return await axios.get(`${api}call/number-by-status`);
  } catch (error) {
    handleError(error);
  }
};
