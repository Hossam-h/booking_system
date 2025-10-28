<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class EventPolicy
{
     public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, Event $event)
    {
        return true;
    }

    public function create(User $user)
    {
        return $user->isOrganizer() || $user->isAdmin();
    }

    public function update(User $user, Event $event)
    {
        return $user->isAdmin() ||( $user->isOrganizer() && $user->id === $event->created_by); 
    }

    public function delete(User $user, Event $event)
    {
        return $user->isAdmin() ||( $user->isOrganizer() && $user->id === $event->created_by);
    }
}
