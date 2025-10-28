<?php

namespace App\Http\Controllers\API;

use App\Models\Payment;
use Illuminate\Http\Request;
use App\Services\PaymentService;
use App\Notifications\BookingConfirmedNotification;
use App\Models\Booking;
use App\Http\Requests\API\PaymentRequest;

class PaymentController extends BaseController
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function store(PaymentRequest $request, $bookingId)
    {
        $booking = Booking::with('ticket')->findOrFail($bookingId);

        if ($booking->user_id !== $request->user()->id) {

            return $this->sendError('Unauthorized');
        }

        if ($booking->payment) {
            return $this->sendError('Payment already processed');
        }

        $amount = $booking->total_amount;
        $result = $this->paymentService->processPayment($amount);

        $payment = Payment::create([
            'booking_id' => $bookingId,
            'amount' => $amount,
            'status' => $result['status'],
        ]);

        if ($result['status'] === 'success') {
            $booking->update(['status' => 'confirmed']);
            $booking->user->notify(new BookingConfirmedNotification($booking));
        }

        return $this->sendResponse($payment, 'Payment processed successfully');
    }

    public function show($id)
    {
        $payment = Payment::with('booking.ticket.event')->findOrFail($id);

        if ($payment->booking->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            return $this->sendError('Unauthorized');
        }

        return $this->sendResponse($payment, 'Payment retrieved successfully');
    }
}
