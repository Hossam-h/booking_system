<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BookingPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Booking $booking): bool
    {
        return ($user->isCustomer() && $booking->user_id === $user->id) || $user->isAdmin();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isOrganizer() || $user->isCustomer();
    }

   

    /**
     * Determine whether the user can delete the model.
     */
    public function cancel(User $user, Booking $booking): bool
    {
        return ($user->isCustomer() && $booking->user_id === $user->id) || $user->isAdmin();
    }



}
