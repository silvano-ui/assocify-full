<?php

namespace Modules\Api\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Modules\Events\Entities\Event;
use Modules\Events\Entities\EventRegistration;

class EventsController extends BaseApiController
{
    public function index()
    {
        return $this->paginate(Event::query());
    }

    public function store(Request $request)
    {
        $event = Event::create($request->all());
        return $this->success($event, 'Event created successfully', 201);
    }

    public function show($id)
    {
        $event = Event::findOrFail($id);
        return $this->success($event);
    }

    public function update(Request $request, $id)
    {
        $event = Event::findOrFail($id);
        $event->update($request->all());
        return $this->success($event, 'Event updated successfully');
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
        return $this->paginate($event->registrations());
    }

    public function register(Request $request, $id)
    {
        $event = Event::findOrFail($id);
        
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
