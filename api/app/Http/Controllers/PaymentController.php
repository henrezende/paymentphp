<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use App\Models\Payment;


class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::all();

        $formattedPayments = $payments->map(function ($payment) {
            return [
                'id' => $payment->id,
                'status' => $payment->status,
                'transaction_amount' => $payment->transaction_amount,
                'installments' => $payment->installments,
                'token' => $payment->token,
                'payment_method_id' => $payment->payment_method_id,
                'payer' => [
                    'entity_type' => $payment->payer_entity_type,
                    'type' => $payment->payer_type,
                    'email' => $payment->payer_email,
                    'identification' => [
                        'type' => $payment->payer_identification_type,
                        'number' => $payment->payer_identification_number,
                    ],
                ],
                'notification_url' => $payment->notification_url,
                'created_at' => Carbon::parse($payment->created_at)->format('Y-m-d'),
                'updated_at' => Carbon::parse($payment->updated_at)->format('Y-m-d'),
            ];
        });

        return response()->json($formattedPayments);
    }

    public function store(Request $request)
    {
        if (empty($request->all())) {
            return response()->json(['message' => 'Payment not provided in the request body'], 400);
        }

        $rules = [
            'transaction_amount' => 'required|numeric',
            'installments' => 'required|integer',
            'token' => 'required|string',
            'payer.email' => 'required|string',
            'payer.identification.type' => 'required|string',
            'payer.identification.number' => 'required|string',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid payment provided. The possible reasons are: ' . $validator->errors()->first(),
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $paymentData = $request->all();
            $paymentData['payer_email'] = $request->input('payer.email');
            $paymentData['payer_identification_type'] = $request->input('payer.identification.type');
            $paymentData['payer_identification_number'] = $request->input('payer.identification.number');
            $payment = Payment::create($paymentData);

            $responseData = [
                'id' => $payment->id,
                'created_at' => Carbon::parse($payment->created_at)->format('Y-m-d')
            ];
            return response()->json($responseData, 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while creating the payment'], 500);
        }
    }

    // public function show($id)
    // {
    //     return Payment::find($id);
    // }

    // public function update(Request $request, $id)
    // {
    //     $payment = Payment::findOrFail($id);
    //     $payment->update($request->all());
    //     return response()->json($payment, 200);
    // }

    // public function destroy($id)
    // {
    //     Payment::findOrFail($id)->delete();
    //     return response()->json(null, 204);
    // }
}


// Lista de pagamentos
// Endpoint: ​GET http://localhost/rest/payments/

// Esse método da API deve retornar uma lista de payments em formato JSON.

// [
//  {
//     "id":"84e8adbf-1a14-403b-ad73-d78ae19b59bf",
//     "status": "CANCELED",
//     "transaction_amount": 245.90,
//     "installments": 3,
//     "token": "ae4e50b2a8f3h6d9f2c3a4b5d6e7f8g9",
//     "payment_method_id": "master",
//     "payer": {
//         "entity_type": "individual",
//         "type": "customer",
//         "email": "example_random@gmail.com",
//         "identification": {
//             "type": "CPF",
//             "number": "12345678909"
//         }
//     },
//     "notification_url": "https://webhook.site/unique-r",
//     "created_at": "2024-01-10"
//     "updated_at": "2024-01-11"
//  },
//  {
//     "id": "9998adbf-1a14-403b-ad73-d78ae19b59bf",    
//     "status": "PAIND",
//     "transaction_amount": 300.90,
//     "installments": 5,
//     "token": "ae4e50b2a8f3h6d965c3a4b5d6e7f8g9",
//     "payment_method_id": "visa",
//     "payer": {
//         "entity_type": "individual",
//         "type": "customer",
//         "email": "example_random2@gmail.com",
//         "identification": {
//             "type": "CPF",
//             "number": "44345678988"
//         }
//     },
//     "notification_url": https://webhook.site/1a2b3c4d",
//     "created_at": "2024-01-10"
//     "updated_at": "2024-01-11"
//  }
// ]


// Ver pagamento
// Confirmar pagamento
// Cancelar um pagamento	