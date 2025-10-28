<?php

namespace App\Http\Controllers\API;

use App\Models\Booking;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Http\Requests\API\CreateBookingRequest;
use App\Notifications\BookingConfirmedNotification;

class BookingController extends BaseController
{
    public function store(CreateBookingRequest $request, $ticketId)
    {
        $this->authorize('create', Booking::class);

        $ticket = Ticket::findOrFail($ticketId);

        $validated = $request->validated();

        if ($ticket->available_quantity < $validated['quantity']) {
            return response()->json([
                'message' => 'Not enough tickets available',
            ], 422);
        }

        $booking = Booking::create([
            'user_id' => $request->user()->id,
            'ticket_id' => $ticketId,
            'quantity' => $validated['quantity'],
            'status' => 'pending',
        ]);




        return $this->sendResponse($booking->load('ticket.event'), 'Booking created successfully');
    }

    public function index(Request $request)
    {
        $bookings = $request->user()
            ->bookings()
            ->with(['ticket.event', 'payment'])
            ->latest()
            ->paginate(15);

        return $this->sendResponse($bookings, 'Bookings retrieved successfully');
    }

    public function cancel(Request $request, $id)
    {

        $booking = Booking::findOrFail($id);
        
        $this->authorize('cancel', $booking);
        if ($booking->status === 'cancelled') {
            return $this->sendError('Booking already cancelled');
        }

        $booking->update(['status' => 'cancelled']);

        return $this->sendResponse($booking, 'Booking cancelled successfully');
    }
}
