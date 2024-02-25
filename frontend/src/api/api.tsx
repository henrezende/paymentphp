import axios from 'axios';
import { IPaymentData } from '../interfaces/home';
const BASE_URL = import.meta.env.VITE_BASE_URL;

export const createPayment = async (paymentData: IPaymentData) => {
  try {
    await axios.post(`${BASE_URL}/payments`, paymentData);
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
  } catch (error: any) {
    const { response } = error;
    throw new Error(response.data.message);
  }
};
