import { describe, expect, test } from 'vitest';
import { render } from '@testing-library/react';
import NewPayment from '../src/Pages/Home.tsx';

describe('NewPayment', () => {
  test('renders the form elements correctly', () => {
    const { getByText, getByTestId } = render(<NewPayment />);
    expect(getByText(/Dados do pagador/i)).toBeInTheDocument();
    expect(getByTestId(/payer_email/i)).toBeInTheDocument();
    expect(getByTestId(/payer_identification_type/i)).toBeInTheDocument();
    expect(getByTestId(/payer_identification_number/i)).toBeInTheDocument();
    expect(getByText(/Dados do pagamento/i)).toBeInTheDocument();
    expect(getByTestId(/transaction_amount/i)).toBeInTheDocument();
    expect(getByTestId(/card_number/i)).toBeInTheDocument();
    expect(getByTestId(/card_holder_name/i)).toBeInTheDocument();
    expect(getByTestId(/expiration_month/i)).toBeInTheDocument();
    expect(getByTestId(/expiration_year/i)).toBeInTheDocument();
    expect(getByTestId(/security_code/i)).toBeInTheDocument();
    expect(getByTestId(/selected_installments/i)).toBeInTheDocument();
    expect(getByText(/Pagar/i)).toBeInTheDocument();
  });

  test('new payment snapshot', () => {
    const result = render(<NewPayment />);
    expect(result).toMatchSnapshot();
  });
});
