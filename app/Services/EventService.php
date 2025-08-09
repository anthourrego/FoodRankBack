<?php

namespace App\Services;
use App\Models\Event;

class EventService
{
    public function create(array $data): Event
    {
      

        return Event::create($data);
    }

    public function get()
    {
        $events = Event::with(['city','eventProducts'])->where('is_active','=',1)->get();
        return $events;
    }
}
