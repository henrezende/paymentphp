import React from 'react';
import { beforeEach, describe, expect, test } from 'vitest';
import { fireEvent, render, screen, waitFor } from '@testing-library/react';
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

  // test('submits the form with valid data', () => {
  //   // const handleSubmit = vi.spyOn(payment, 'create');
  //   const handleSubmit = jest.fn();
  //   const { getByText, getByTestId } = render(
  //     <NewPayment onSubmit={handleSubmit} />,
  //   );
  //   fireEvent.change(getByTestId(/payer_email/i), {
  //     target: { value: 'teste@test.com' },
  //   });
  //   fireEvent.change(getByTestId(/payer_identification_type/i), {
  //     target: { value: 'CPF' },
  //   });
  //   fireEvent.change(getByTestId(/payer_identification_number/i), {
  //     target: { value: '123123123' },
  //   });
  //   fireEvent.change(getByTestId(/transaction_amount/i), {
  //     target: { value: '100' },
  //   });
  //   fireEvent.change(getByTestId(/card_number/i), {
  //     target: { value: '1234567890123456' },
  //   });
  //   fireEvent.change(getByTestId(/card_holder_name/i), {
  //     target: { value: 'test test' },
  //   });
  //   fireEvent.change(getByTestId(/expiration_month/i), {
  //     target: { value: '11' },
  //   });
  //   fireEvent.change(getByTestId(/expiration_year/i), {
  //     target: { value: '2050' },
  //   });
  //   fireEvent.change(getByTestId(/security_code/i), {
  //     target: { value: '123' },
  //   });
  //   fireEvent.change(getByTestId(/selected_installments/i), {
  //     target: { value: '1' },
  //   });

  //   fireEvent.click(getByText(/Pagar/i));

  //   expect(handleSubmit).toHaveBeenCalledWith({
  //     payer_email: 'teste@test.com',
  //     payer_identification_type: 'CPF',
  //     payer_identification_number: '123123123',
  //     transaction_amount: '100',
  //     card_number: '1234567890123456',
  //     card_holder_name: 'test test',
  //     expiration_month: '11',
  //     expiration_year: '2050',
  //     security_code: '123',
  //     selected_installments: '1',
  //   });
  // });
});
