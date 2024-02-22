<?php

namespace Database\Factories;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Payment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'transaction_amount' => $this->faker->randomFloat(2, 10, 1000),
            'installments' => $this->faker->numberBetween(1, 12),
            'token' => $this->faker->uuid,
            'payment_method_id' => $this->faker->randomElement(['visa', 'mastercard', 'amex']),
            'payer_entity_type' => 'individual',
            'payer_type' => 'customer',
            'payer_email' => $this->faker->unique()->safeEmail,
            'payer_identification_type' => 'CPF',
            'payer_identification_number' => $this->faker->numerify('###########'),
            'notification_url' => $this->faker->url,
            'status' => $this->faker->randomElement(['PENDING', 'PAID', 'CANCELED']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
