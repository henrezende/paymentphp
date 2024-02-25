import axios from 'axios';
import { IPaymentData } from '../interfaces/home';
const BASE_URL = import.meta.env.BASE_URL;

export const createPayment = async (paymentData: IPaymentData) => {
  try {
    await axios.post(`${BASE_URL}/payments`, paymentData);
  } catch (error: any) {
    const { response } = error;
    throw new Error(response.data.message);
  }
};
