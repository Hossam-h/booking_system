<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Booking;

class PreventDoubleBooking
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
     public function handle(Request $request, Closure $next)
    {
        if ($request->route()->getName() === 'bookings.store') {
            $ticketId = $request->route('ticket');
            $userId = $request->user()->id;

            $existingBooking = Booking::where('user_id', $userId)
                ->where('ticket_id', $ticketId)
                ->whereIn('status', ['pending', 'confirmed'])
                ->exists();

            if ($existingBooking) {
                return response()->json([
                    'message' => 'You already have an active booking for this ticket',
                ], 422);
            }
        }

        return $next($request);
    }
}
