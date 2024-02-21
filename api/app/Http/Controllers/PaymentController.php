<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
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
            return response()->json(['message' => 'Payment not provided in the request body'], Response::HTTP_BAD_REQUEST);
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
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
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
            return response()->json($responseData, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while creating the payment'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id)
    {
        $payment = Payment::find($id);

        if (!$payment) {
            return response()->json(['message' => 'Payment not found'], Response::HTTP_NOT_FOUND);
        }

        $formattedPayment = [
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

        return response()->json($formattedPayment);
    }

    public function update(Request $request, $id)
    {
        $payment = Payment::find($id);

        if (!$payment) {
            return response()->json(['message' => 'Bankslip not found with the specified id'], Response::HTTP_NOT_FOUND);
        }

        $request->validate([
            'status' => 'required|in:PAID',
        ]);

        $payment->status = $request->input('status');
        $payment->save();

        return response()->noContent();
    }

    public function destroy($id)
    {
        $payment = Payment::find($id);

        if (!$payment) {
            return response()->json(['message' => 'Payment not found with the specified id'], Response::HTTP_NOT_FOUND);
        }

        $payment->status = 'CANCELED';
        $payment->save();

        return response()->noContent();
    }
}
