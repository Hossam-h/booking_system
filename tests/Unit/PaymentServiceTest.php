<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\PaymentService;

class PaymentServiceTest extends TestCase
{
    public function test_process_payment_returns_expected_structure(): void
    {
        $service = new PaymentService();

        $amount = 123.45;
        $result = $service->processPayment($amount);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('status', $result);
        $this->assertArrayHasKey('amount', $result);

        $this->assertContains($result['status'], ['success', 'failed']);
        $this->assertSame($amount, $result['amount']);
    }

  
}
