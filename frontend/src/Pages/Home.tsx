import React, { useState, useEffect } from 'react';
import { initMercadoPago, getInstallments } from '@mercadopago/sdk-react';
import { createCardToken } from '@mercadopago/sdk-react/coreMethods';
import CurrencyInput from 'react-currency-input-field';
import InputMask from 'react-input-mask';
import { createPayment } from '../api/api';
import { IInstallmentOption, IPaymentForm } from '../interfaces/home';
import { ToastContainer, toast } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';

const NewPayment: React.FC<IPaymentForm> = () => {
  const [formData, setFormData] = useState<IPaymentForm>({
    transaction_amount: '',
    installment_options: [],
    token: '',
    payment_method_id: '',
    payer_email: '',
    payer_identification_type: '',
    payer_identification_number: '',
    card_number: '',
    card_holder_name: '',
    expiration_month: '',
    expiration_year: '',
    security_code: '',
    selected_installments: 1,
  });

  const handleInputChange = (
    e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>,
  ) => {
    const { name, value } = e.target;
    setFormData((prevFormData: IPaymentForm) => ({
      ...prevFormData,
      [name]: value,
    }));
  };

  const handleTransactionAmountChange = (value: string | undefined) => {
    if (value) {
      const formattedValue = value.replace(/\./g, '').replace(',', '.');

      setFormData((prevFormData: IPaymentForm) => ({
        ...prevFormData,
        transaction_amount: formattedValue,
      }));
    }
  };

  const handleSubmit = async (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault();

    try {
      const cardToken = await createCardToken({
        cardNumber: formData.card_number,
        cardholderName: formData.card_holder_name,
        cardExpirationMonth: formData.expiration_month,
        cardExpirationYear: formData.expiration_year,
        securityCode: formData.security_code,
        identificationType: formData.payer_identification_type,
        identificationNumber: formData.payer_identification_number,
      });

      const paymentData = {
        transaction_amount: formData.transaction_amount,
        installments: formData.selected_installments,
        token: cardToken?.id,
        payment_method_id: formData.payment_method_id,
        payer: {
          email: formData.payer_email,
          identification: {
            type: formData.payer_identification_type,
            number: formData.payer_identification_number,
          },
        },
      };

      await createPayment(paymentData);

      toast.success('Pagamento criado com sucesso!', {
        autoClose: 3000,
        position: 'top-right',
        theme: 'colored',
        onClose: () => window.location.reload(),
      });

      // eslint-disable-next-line @typescript-eslint/no-explicit-any
    } catch (error: any) {
      const errorMessage = Array.isArray(error)
        ? error[0].message
        : error.message;
      toast.error(errorMessage, {
        position: 'top-right',
        theme: 'colored',
      });
      console.error('Error creating payment:', error);
    }
  };

  const identificationTypes = [
    { value: 'CPF', label: 'CPF' },
    { value: 'RG', label: 'RG' },
  ];

  useEffect(() => {
    initMercadoPago(import.meta.env.VITE_MP_KEY);
  }, []);

  useEffect(() => {
    if (
      formData.transaction_amount &&
      formData.card_number.toString().length === 16
    ) {
      const fetchInstallments = async () => {
        try {
          await getInstallments({
            amount: formData.transaction_amount.toString(),
            locale: 'pt-BR',
            bin: formData.card_number,
          }).then((installment_options) => {
            const installmentOptionsFormatted =
              installment_options?.[0]?.payer_costs?.map(
                (opt: IInstallmentOption) => ({
                  installment_amount: opt.installment_amount,
                  recommended_message: opt.recommended_message,
                  payment_method_option_id: opt.payment_method_option_id,
                  installments: opt.installments,
                }),
              ) ?? [];

            setFormData((prevFormData: IPaymentForm) => ({
              ...prevFormData,
              installment_options: installmentOptionsFormatted,
              payment_method_id:
                installment_options?.[0]?.payment_method_id ?? '',
            }));
          });
        } catch (error) {
          console.error('Error getting installment_options:', error);
        }
      };

      fetchInstallments();
    }
  }, [formData.transaction_amount, formData.card_number]);

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
                  name="payer_email"
                  placeholder="E-mail do pagador"
                  value={formData.payer_email}
                  onChange={handleInputChange}
                  className="w-full p-2 border rounded-md"
                  required
                />
              </label>
            </div>
            <div className="w-full mb-4">
              <select
                id="payer_identification_type"
                name="payer_identification_type"
                value={formData.payer_identification_type}
                onChange={handleInputChange}
                className="appearance-none border rounded bg-white w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
              >
                <option
                  disabled
                  label="Selecione o tipo de documento de identificação"
                ></option>
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
                name="payer_identification_number"
                value={formData.payer_identification_number}
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
                id="transaction_amount"
                name="transaction_amount"
                prefix="R$"
                value={formData.transaction_amount}
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
                name="card_number"
                mask="9999999999999999"
                value={formData.card_number}
                onChange={handleInputChange}
                className="w-full p-2 border rounded-md"
                placeholder="Número do cartão"
                maskPlaceholder={null}
                required
              />
            </div>
            <div className="w-full mb-4">
              <input
                type="text"
                name="card_holder_name"
                value={formData.card_holder_name}
                onChange={handleInputChange}
                className="w-full p-2 border rounded-md"
                placeholder="Nome do titular"
                required
              />
            </div>
            <div className="w-full mb-4">
              <InputMask
                type="text"
                name="expiration_month"
                value={formData.expiration_month}
                onChange={handleInputChange}
                className="w-full p-2 border rounded-md"
                placeholder="Mês de expiração (MM)"
                mask="99"
                required
              />
            </div>
            <div className="w-full mb-4">
              <InputMask
                type="text"
                name="expiration_year"
                value={formData.expiration_year}
                onChange={handleInputChange}
                className="w-full p-2 border rounded-md"
                placeholder="Ano de expiração (YYYY)"
                mask="9999"
                required
              />
            </div>
            <div className="w-full mb-4">
              <input
                type="text"
                name="security_code"
                value={formData.security_code}
                onChange={handleInputChange}
                className="w-full p-2 border rounded-md"
                placeholder="CVV"
                required
              />
            </div>
            <div className="w-full mb-4">
              <select
                name="selected_installments"
                value={formData.selected_installments}
                onChange={handleInputChange}
                className="w-full p-2 border rounded-md bg-white"
                required
              >
                <option
                  disabled
                  label="Selecione o número de parcelas"
                ></option>
                {formData.installment_options?.map((option) => (
                  <option key={option.installments} value={option.installments}>
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
      <ToastContainer />
    </>
  );
};

export default NewPayment;
