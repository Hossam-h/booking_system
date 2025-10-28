<?php
namespace App\Services;

class PaymentService
{
    public function processPayment(float $amount): array
    {
        // Simulate payment processing
        $success = rand(0, 100) > 10; // 90% success rate

        return [
            'status' => $success ? 'success' : 'failed',
            'amount' => $amount,
        ];
    }
}