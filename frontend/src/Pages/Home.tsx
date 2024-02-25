'use client';

import React, { useState, useEffect } from 'react';
import { initMercadoPago, getInstallments } from '@mercadopago/sdk-react';
import CurrencyInput from 'react-currency-input-field';
import InputMask from 'react-input-mask';
import axios from '../../axios-config';

interface InstallmentOption {
  recommended_message: string;
  installment_amount: number;
  payment_method_option_id: string;
}

// delete
interface NewPaymentProps {
  installmentOptions: InstallmentOption[];
}

interface PaymentData {
  transactionAmount: string;
  installmentOptions: InstallmentOption[];
  token: string;
  paymentMethodId: string;
  cardNumber: string;
  cardOwnerName: string;
  expirationMonth: string;
  expirationYear: string;
  cvv: string;
  selectedInstallments: string;
  payerEmail: string;
  payerIdentificationType: string;
  payerIdentificationNumber: string;
}

interface PayerCosts {
  installment_amount: number;
  recommended_message: string;
  payment_method_option_id: string;
}

const NewPayment: React.FC<NewPaymentProps> = ({ installmentOptions }) => {
  const [formData, setFormData] = useState<PaymentData>({
    transactionAmount: '',
    installmentOptions: [],
    token: '',
    paymentMethodId: '',
    payerEmail: '',
    payerIdentificationType: '',
    payerIdentificationNumber: '',
    cardNumber: '',
    cardOwnerName: '',
    expirationMonth: '',
    expirationYear: '',
    cvv: '',
    selectedInstallments: '',
  });

  const handleInputChange = (
    e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>
  ) => {
    const { name, value } = e.target;
    setFormData((prevFormData: PaymentData) => ({
      ...prevFormData,
      [name]: value,
    }));
  };

  const handleTransactionAmountChange = (value: string | undefined) => {
    if (value) {
      const formattedValue = value.replace(/\./g, '').replace(',', '.');

      setFormData((prevFormData: PaymentData) => ({
        ...prevFormData,
        transactionAmount: formattedValue,
      }));
    }
  };

  const handleSubmit = async (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault();
    await axios
      .post('/payments', JSON.stringify(formData), {
        headers: {
          'Content-Type': 'application/json',
        },
      })
      .then(() => {
        console.log('Payment created successfully');
      })
      .catch((err) => {
        console.error('Error creating payment:', err);
      });
  };

  const identificationTypes = [
    { value: 'CPF', label: 'CPF' },
    { value: 'RG', label: 'RG' },
  ];

  useEffect(() => {
    initMercadoPago('TEST-b1968cca-0d6c-4498-a7c3-854d5901c8f8');
  }, []);

  useEffect(() => {
    if (formData.transactionAmount && formData.cardNumber) {
      fetchInstallments();
    }
  }, [formData.transactionAmount, formData.cardNumber]);

  const fetchInstallments = async () => {
    try {
      await getInstallments({
        amount: formData.transactionAmount.toString(),
        locale: 'pt-BR',
        bin: formData.cardNumber,
      }).then((installmentOptions) => {
        const installmentOptionsFormatted =
          installmentOptions?.[0]?.payer_costs?.map((opt: PayerCosts) => ({
            installment_amount: opt.installment_amount,
            recommended_message: opt.recommended_message,
            payment_method_option_id: opt.payment_method_option_id,
          })) ?? [];

        setFormData((prevFormData: PaymentData) => ({
          ...prevFormData,
          installmentOptions: installmentOptionsFormatted,
        }));
        console.log(
          'installmentOptionsFormatted:',
          installmentOptionsFormatted
        );
      });
    } catch (error) {
      console.error('Error getting installmentOptions:', error);
    }
  };

  return (
    <>
      <div className="flex justify-center p-4">
        <form
          className="flex flex-wrap px-8 py-8 bg-gray-50 rounded-lg xl:max-w-7xl"
          onSubmit={handleSubmit}
        >
          <div className="flex flex-wrap mb-4 md:w-1/2 sm:w-full px-4 content-start">
            <span className="mb-4 text-3xl font-bold">Dados do pagador</span>
            <div className="w-full mb-4">
              <label className="block mb-2">
                <input
                  type="email"
                  name="payerEmail"
                  placeholder="E-mail do pagador"
                  value={formData.payerEmail}
                  onChange={handleInputChange}
                  className="w-full p-2 border rounded-md"
                  required
                />
              </label>
            </div>
            <div className="w-full mb-4">
              <select
                id="payerIdentificationType"
                name="payerIdentificationType"
                value={formData.payerIdentificationType}
                onChange={handleInputChange}
                className="appearance-none border rounded bg-white w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
              >
                {identificationTypes.map((option) => (
                  <option key={option.value} value={option.value}>
                    {option.label}
                  </option>
                ))}
              </select>
            </div>
            <div className="w-full mb-4">
              <input
                type="text"
                name="payerIdentificationNumber"
                value={formData.payerIdentificationNumber}
                onChange={handleInputChange}
                className="w-full p-2 border rounded-md"
                placeholder="Número de identificação"
                required
              />
            </div>
          </div>

          <div className="flex flex-wrap mb-4 md:w-1/2 sm:w-full px-4 content-start">
            <span className="mb-4 text-3xl font-bold">Dados do pagamento</span>

            <div className="w-full mb-4">
              <CurrencyInput
                id="transactionAmount"
                name="transactionAmount"
                prefix="R$"
                value={formData.transactionAmount}
                onValueChange={(value) => handleTransactionAmountChange(value)}
                className="w-full p-2 border rounded-md"
                placeholder="Valor do pagamento"
                fixedDecimalLength={2}
                groupSeparator="."
                decimalSeparator=","
                required
              />
            </div>
            <div className="w-full mb-4">
              <InputMask
                type="text"
                name="cardNumber"
                mask="9999999999999999"
                value={formData.cardNumber}
                onChange={handleInputChange}
                className="w-full p-2 border rounded-md"
                placeholder="Número do cartão"
                required
              />
            </div>
            <div className="w-full mb-4">
              <input
                type="text"
                name="cardOwnerName"
                value={formData.cardOwnerName}
                onChange={handleInputChange}
                className="w-full p-2 border rounded-md"
                placeholder="Nome do titular"
                required
              />
            </div>
            <div className="w-full mb-4">
              <input
                type="text"
                name="expirationMonth"
                value={formData.expirationMonth}
                onChange={handleInputChange}
                className="w-full p-2 border rounded-md"
                placeholder="Mês de expiração (MM)"
                required
              />
            </div>
            <div className="w-full mb-4">
              <input
                type="text"
                name="expirationYear"
                value={formData.expirationYear}
                onChange={handleInputChange}
                className="w-full p-2 border rounded-md"
                placeholder="Ano de expiração (YYYY)"
                required
              />
            </div>
            <div className="w-full mb-4">
              <input
                type="text"
                name="cvv"
                value={formData.cvv}
                onChange={handleInputChange}
                className="w-full p-2 border rounded-md"
                placeholder="CVV"
                required
              />
            </div>
            <div className="w-full mb-4">
              <select
                name="selectedInstallments"
                value={formData.selectedInstallments}
                onChange={handleInputChange}
                className="w-full p-2 border rounded-md bg-white"
                required
              >
                <option label="Selecione o número de parcelas"></option>
                {formData.installmentOptions?.map((option) => (
                  <option
                    key={option.installment_amount}
                    value={option.installment_amount.toString()}
                  >
                    {option.recommended_message}
                  </option>
                ))}
              </select>
            </div>
          </div>

          <div className="flex text-center justify-center w-full px-4">
            <button
              type="submit"
              className="bg-green-500 sm:w-full lg:w-1/2 text-white py-2 px-4 rounded-md"
            >
              Pagar
            </button>
          </div>
        </form>
      </div>
    </>
  );
};

export default NewPayment;
