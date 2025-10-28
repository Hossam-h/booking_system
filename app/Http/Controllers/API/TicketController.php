<?php

namespace App\Http\Controllers\API;

use App\Models\Ticket;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Http\Requests\API\TicketRequest;

class TicketController extends BaseController
{
  
    /**
     * Store a newly created resource in storage.
     */
    public function store($eventId,TicketRequest $request)
    {
        $this->authorize('create', Ticket::class);
        $validated = $request->validated();


        $ticket = Ticket::create([
          'event_id' => $eventId,
          ...$validated
        ]);

        return $this->sendResponse($ticket, 'Ticket created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Ticket $ticket)
    {
        $this->authorize('view', $ticket);
        return $this->sendResponse($ticket, 'Ticket retrieved successfully');
    }

   

    /**
     * Update the specified resource in storage.
     */
    public function update(TicketRequest $request, Ticket $ticket)
    {
        
        $this->authorize('update', $ticket);
        $validated = $request->validated();

        $ticket->update($validated);

        return $this->sendResponse($ticket, 'Ticket updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ticket $ticket)
    {
        $this->authorize('delete', $ticket);
        $ticket->delete();

        return $this->sendResponse(null, 'Ticket deleted successfully');
    }
}
