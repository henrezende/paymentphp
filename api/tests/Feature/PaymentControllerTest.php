<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

use App\Models\Payment;

class PaymentControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function test_store_method_creates_payment()
    {
        $data = [
            'transaction_amount' => 100.00,
            'installments' => 1,
            'token' => "ae4e50b2a8f3h6d9f2c3a4b5d6e7f8g9",
            'payment_method_id' => 'master',
            'payer' => [
                'email' => 'example_random@gmail.com',
                'identification' => [
                    'type' => 'CPF',
                    'number' => '12345678909'
                ]
            ]
        ];

        $response = $this->postJson('/rest/payments', $data);

        $response->assertStatus(201)
            ->assertJsonStructure(['id', 'created_at']);
    }

    public function test_store_method_with_empty_data()
    {
        $response = $this->postJson('/rest/payments', []);

        $response->assertStatus(400)
            ->assertJson(['message' => 'Payment not provided in the request body']);
    }

    public function test_store_method_with_invalid_data()
    {
        $invalidData = [
            'installments' => 3,
            'payment_method_id' => 'master',
            'payer' => [
                'email' => 'example_random@gmail.com',
                'identification' => [
                    'type' => 'CPF',
                    'number' => '12345678909'
                ]
            ]
        ];

        $response = $this->postJson('/rest/payments', $invalidData);

        $response->assertStatus(422)
            ->assertJsonStructure(['message', 'errors']);
    }

    public function test_store_method_with_field_too_large()
    {
        $data = [
            'transaction_amount' => 245.90,
            'installments' => 3,
            'token' => 'ae4e50b2a8f3h6d9f2c3a4b5d6e7f8g9',
            'payment_method_id' => 'master',
            'payer' => [
                'email' => str_repeat('a', 256),
                'identification' => [
                    'type' => 'CPF',
                    'number' => '12345678909'
                ]
            ]
        ];

        $response = $this->postJson('/rest/payments', $data);

        $response->assertStatus(500)
            ->assertJson(['message' => 'An error occurred while creating the payment']);
    }

    public function test_show_method_returns_payment()
    {
        $payment = Payment::factory()->create();

        $response = $this->getJson("/rest/payments/{$payment->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'status',
                'transaction_amount',
                'installments',
                'token',
                'payment_method_id',
                'payer' => [
                    'entity_type',
                    'type',
                    'email',
                    'identification' => [
                        'type',
                        'number',
                    ],
                ],
                'notification_url',
                'created_at',
                'updated_at',
            ]);
    }

    public function test_show_method_returns_not_found_for_invalid_id()
    {
        $response = $this->getJson('/rest/payments/123');

        $response->assertStatus(404)
            ->assertJson(['message' => 'Payment not found']);
    }

    public function test_index_method_returns_list_of_payments()
    {
        Payment::factory()->count(5)->create();

        $response = $this->getJson('/rest/payments');

        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'status',
                    'transaction_amount',
                    'installments',
                    'token',
                    'payment_method_id',
                    'payer' => [
                        'entity_type',
                        'type',
                        'email',
                        'identification' => [
                            'type',
                            'number',
                        ],
                    ],
                    'notification_url',
                    'created_at',
                    'updated_at',
                ],
            ])
            ->assertJsonCount(5);
    }

    public function test_update_method_updates_payment_status()
    {
        $payment = Payment::factory()->create();
        $newStatus = 'PAID';
        $response = $this->patchJson("/rest/payments/{$payment->id}", ['status' => $newStatus]);

        $payment->refresh();

        $response->assertStatus(204)->assertNoContent();
        $this->assertEquals($newStatus, $payment->status);
    }

    public function test_update_method_returns_not_found_for_invalid_id()
    {
        $response = $this->patchJson('/rest/payments/123', ['status' => 'PAID']);

        $response->assertStatus(404)
            ->assertJson(['message' => 'Bankslip not found with the specified id']);
    }

    public function test_destroy_method_cancels_payment()
    {
        $payment = Payment::factory()->create();
        $response = $this->delete("/rest/payments/{$payment->id}");
        $payment->refresh();

        $response->assertStatus(204)->assertNoContent();
        $this->assertEquals('CANCELED', $payment->status);
    }

    public function test_destroy_method_returns_not_found_for_invalid_id()
    {
        $response = $this->delete('/rest/payments/123');
        $response->assertStatus(404)->assertJson(['message' => 'Payment not found with the specified id']);
    }
}
