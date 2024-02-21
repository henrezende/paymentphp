<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\Payment;

class PaymentControllerTest extends TestCase
{
    use RefreshDatabase;

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

    /* TODO: 
    index
    show
    update
    destroy
    */
}
