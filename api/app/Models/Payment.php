<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Payment extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'transaction_amount',
        'installments',
        'token',
        'payment_method_id',
        'payer_entity_type',
        'payer_type',
        'payer_email',
        'payer_identification_type',
        'payer_identification_number',
        'notification_url',
        'status',
    ];

    protected $casts = [
        'transaction_amount' => 'float',
        'installments' => 'integer',
        'created_at' => 'date:Y-m-d',
    ];

    protected $attributes = [
        'payer_entity_type' => 'individual',
        'payer_type' => 'customer',
        'status' => 'PENDING',
        'notification_url' => 'https://webhook.site/8b9f8513-2b2a-4be9-a6f3-19dc3b02591c'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = Str::uuid()->toString();
            }
        });
    }
}
