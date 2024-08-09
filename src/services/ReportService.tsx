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

export const reportCallCountForTheYear = async () => {
  try {
    return await axios.get(`${api}call/count-for-the-year`);
  } catch (error) {
    handleError(error);
  }
};

export const reportCallCountRepeatForTheYear = async () => {
  try {
    return await axios.get(`${api}call/count-repeat-for-the-year`);
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

export const reportHospitalAmountOfRewardForTheYear = async () => {
  try {
    return await axios.get(`${api}hospital/amount-of-reward-for-the-year`);
  } catch (error) {
    handleError(error);
  }
};

export const reportHospitalCountForTheYear = async () => {
  try {
    return await axios.get(`${api}hospital/count-for-the-year`);
  } catch (error) {
    handleError(error);
  }
};
