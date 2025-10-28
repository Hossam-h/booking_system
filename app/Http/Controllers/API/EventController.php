<?php

namespace App\Http\Controllers\API;

use App\Models\Event;
use App\Http\Requests\API\EventStoreRequest;
use App\Http\Requests\API\EventUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class EventController extends BaseController
{
    public function index(Request $request)
    {
        $cacheKey = 'events_' . md5(json_encode($request->all()));
        
        return Cache::remember($cacheKey, 600, function () use ($request) {
            $query = Event::with('tickets');

            if ($request->has('search')) {
                $query->searchByTitle($request->search);
            }

            if ($request->has('date')) {
                $query->filterByDate($request->date);
            }

            if ($request->has('location')) {
                $query->where('location', 'like', '%' . $request->location . '%');
            }

            return $this->sendResponse($query->paginate(15), 'Events retrieved successfully');
        });
    }

    public function show($id)
    {
        $event = Event::with('tickets')->findOrFail($id);
        return $this->sendResponse($event, 'Event retrieved successfully');
    }

    public function store(EventStoreRequest $request)
    {
        $this->authorize('create', Event::class);

        $validated = $request->validated();

        $event = Event::create([
            ...$validated,
            'created_by' => $request->user()->id,
        ]);


        return $this->sendResponse($event, 'Event created successfully');
    }

    public function update(EventUpdateRequest $request, $id)
    {
        $event = Event::findOrFail($id);
        $this->authorize('update', $event);

        $validated = $request->validated();

        $event->update($validated);


        return $this->sendResponse($event, 'Event updated successfully');
    }

    public function destroy($id)
    {
        $event = Event::findOrFail($id);
        $this->authorize('delete', $event);

        $event->delete();


        return $this->sendResponse(null, 'Event deleted successfully');
    }
}