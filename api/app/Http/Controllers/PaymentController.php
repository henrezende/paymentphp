<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use App\Models\Payment;

/**
 * @OA\Info(
 *     title="PaymentPHP Swagger API documentation",
 *     version="1.0.0",
 *     @OA\Contact(
 *         email="henrique@email.com"
 *     ),
 * )
 */
class PaymentController extends Controller
{
    /**
     * @OA\Get(
     *     path="/rest/payments",
     *     summary="Retrieve a list of payments",
     *     tags={"Payments"},
     *     operationId="getPayments",
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="string", example="84e8adbf-1a14-403b-ad73-d78ae19b59bf"),
     *                 @OA\Property(property="status", type="string", example="PENDING"),
     *                 @OA\Property(property="transaction_amount", type="number", format="float", example="245.90"),
     *                 @OA\Property(property="installments", type="integer", format="int32", example="3"),
     *                 @OA\Property(property="token", type="string", example="ae4e50b2a8f3h6d9f2c3a4b5d6e7f8g9"),
     *                 @OA\Property(property="payment_method_id", type="string", example="master"),
     *                 @OA\Property(
     *                     property="payer",
     *                     type="object",
     *                     @OA\Property(property="entity_type", type="string", example="individual"),
     *                     @OA\Property(property="type", type="string", example="customer"),
     *                     @OA\Property(property="email", type="string", example="example_random@gmail.com"),
     *                     @OA\Property(
     *                         property="identification",
     *                         type="object",
     *                         @OA\Property(property="type", type="string", example="CPF"),
     *                         @OA\Property(property="number", type="string", example="12345678909"),
     *                     ),
     *                 ),
     *                 @OA\Property(property="notification_url", type="string", example="https://webhook.site/unique-r"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-10T12:00:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-11T12:00:00Z"),
     *             ),
     *         ),
     *     ),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/rest/payments",
     *     summary="Create a new payment",
     *     tags={"Payments"},
     *     operationId="createPayment",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Payment details",
     *         @OA\JsonContent(
     *             required={"transaction_amount", "installments", "token", "payment_method_id", "payer"},
     *             @OA\Property(property="transaction_amount", type="number", format="float", example="100.00"),
     *             @OA\Property(property="installments", type="integer", format="int32", example="3"),
     *             @OA\Property(property="token", type="string", example="ae4e50b2a8f3h6d9f2c3a4b5d6e7f8g9"),
     *             @OA\Property(property="payment_method_id", type="string", example="master"),
     *             @OA\Property(
     *                 property="payer",
     *                 type="object",
     *                 @OA\Property(property="entity_type", type="string", example="individual"),
     *                 @OA\Property(property="type", type="string", example="customer"),
     *                 @OA\Property(property="email", type="string", example="example_random@gmail.com"),
     *                 @OA\Property(
     *                     property="identification",
     *                     type="object",
     *                     @OA\Property(property="type", type="string", example="CPF"),
     *                     @OA\Property(property="number", type="string", example="12345678909"),
     *                 ),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Payment created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="string", example="84e8adbf-1a14-403b-ad73-d78ae19b59bf"),
     *             @OA\Property(property="created_at", type="string", format="date-time", example="2024-02-22T12:00:00Z"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object", example={"transaction_amount": {"The transaction amount field is required."}}),
     *         ),
     *     ),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/rest/payments/{id}",
     *     summary="Retrieve a payment by ID",
     *     tags={"Payments"},
     *     operationId="getPaymentById",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the payment",
     *         @OA\Schema(type="string", format="uuid"),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="string", example="84e8adbf-1a14-403b-ad73-d78ae19b59bf"),
     *             @OA\Property(property="status", type="string", example="PENDING"),
     *             @OA\Property(property="transaction_amount", type="number", format="float", example="245.90"),
     *             @OA\Property(property="installments", type="integer", format="int32", example="3"),
     *             @OA\Property(property="token", type="string", example="ae4e50b2a8f3h6d9f2c3a4b5d6e7f8g9"),
     *             @OA\Property(property="payment_method_id", type="string", example="master"),
     *             @OA\Property(
     *                 property="payer",
     *                 type="object",
     *                 @OA\Property(property="entity_type", type="string", example="individual"),
     *                 @OA\Property(property="type", type="string", example="customer"),
     *                 @OA\Property(property="email", type="string", example="example_random@gmail.com"),
     *                 @OA\Property(
     *                     property="identification",
     *                     type="object",
     *                     @OA\Property(property="type", type="string", example="CPF"),
     *                     @OA\Property(property="number", type="string", example="12345678909"),
     *                 ),
     *             ),
     *             @OA\Property(property="notification_url", type="string", example="https://webhook.site/unique-r"),
     *             @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-10T12:00:00Z"),
     *             @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-11T12:00:00Z"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Payment not found"),
     *         ),
     *     ),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     */
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

    /**
     * @OA\Patch(
     *     path="/rest/payments/{id}",
     *     summary="Update the status of a payment",
     *     tags={"Payments"},
     *     operationId="updatePaymentStatus",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the payment",
     *         @OA\Schema(type="string", format="uuid"),
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Payment status",
     *         @OA\JsonContent(
     *             required={"status"},
     *             @OA\Property(property="status", type="string", example="PAID"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="No Content",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Payment not found"),
     *         ),
     *     ),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     */
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

    /**
     * @OA\Delete(
     *     path="/rest/payments/{id}",
     *     summary="Cancel a payment",
     *     tags={"Payments"},
     *     operationId="cancelPayment",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the payment",
     *         @OA\Schema(type="string", format="uuid"),
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="No Content",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Payment not found"),
     *         ),
     *     ),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     */
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
