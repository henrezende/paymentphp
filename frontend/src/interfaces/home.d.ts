export interface IInstallmentOption {
  recommended_message: string;
  installment_amount: number;
  payment_method_option_id: string;
  installments: number;
}

export interface IPaymentForm {
  transaction_amount: string;
  installment_options: InstallmentOption[];
  token: string;
  payment_method_id: string;
  card_number: string;
  card_holder_name: string;
  expiration_month: string;
  expiration_year: string;
  security_code: string;
  selected_installments: number;
  payer_email: string;
  payer_identification_type: string;
  payer_identification_number: string;
}

export interface IPaymentData {
  transaction_amount: string;
  installments: number;
  token: string | undefined;
  payer: IPayer;
}

interface IPayer {
  email: string;
  identification: IPayerIdentification;
}

interface IPayerIdentification {
  type: string;
  number: string;
}
