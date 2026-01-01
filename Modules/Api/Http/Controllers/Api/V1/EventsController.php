<?php

namespace Modules\Api\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Modules\Events\Entities\Event;
use Modules\Events\Entities\EventRegistration;
use Modules\Api\Http\Requests\V1\StoreEventRequest;
use Modules\Api\Http\Requests\V1\UpdateEventRequest;
use Modules\Api\Http\Resources\V1\EventResource;

class EventsController extends BaseApiController
{
    public function index()
    {
        return $this->paginate(Event::query(), 15, EventResource::class);
    }

    public function store(StoreEventRequest $request)
    {
        $data = $request->validated();
        if (!isset($data['created_by'])) {
            $data['created_by'] = auth()->id();
        }
        
        $event = Event::create($data);
        return $this->success(new EventResource($event), 'Event created successfully', 201);
    }

    public function show($id)
    {
        $event = Event::with(['category', 'createdBy'])->findOrFail($id);
        return $this->success(new EventResource($event));
    }

    public function update(UpdateEventRequest $request, $id)
    {
        $event = Event::findOrFail($id);
        $event->update($request->validated());
        return $this->success(new EventResource($event), 'Event updated successfully');
    }

    public function destroy($id)
    {
        $event = Event::findOrFail($id);
        $event->delete();
        return $this->success([], 'Event deleted successfully');
    }

    public function registrations($id)
    {
        $event = Event::findOrFail($id);
        // Assuming we might want a resource for registrations too, but for now simple pagination
        return $this->paginate($event->registrations());
    }

    public function register(Request $request, $id)
    {
        $event = Event::findOrFail($id);
        
        $request->validate([
            'first_name' => 'required_without:user_id|string',
            'last_name' => 'required_without:user_id|string',
            'email' => 'required_without:user_id|email',
            'ticket_type_id' => 'nullable|exists:event_ticket_types,id',
        ]);

        $data = $request->all();
        $data['event_id'] = $event->id;
        
        if (!isset($data['user_id']) && auth()->check()) {
            $data['user_id'] = auth()->id();
        }
        
        $registration = EventRegistration::create($data);
        return $this->success($registration, 'Registered for event successfully');
    }

    public function unregister($id, $user_id)
    {
        $registration = EventRegistration::where('event_id', $id)
            ->where('user_id', $user_id)
            ->firstOrFail();
            
        $registration->delete();
        return $this->success([], 'Unregistered from event successfully');
    }
}
