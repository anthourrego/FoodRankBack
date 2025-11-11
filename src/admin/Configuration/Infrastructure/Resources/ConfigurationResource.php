<?php

namespace src\admin\Configuration\Infrastructure\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class ConfigurationResource extends JsonResource
{
    

    public function toArray(Request $request): array
    {
        

        return [
            'id' => $this->id,
            'key' => $this->key,
            'value' => $this->value,
            'type' => $this->type,
            'eventId' => $this->event_id,
            'description' => $this->description,
            'isActive' => $this->is_active,
            'createdAt' => $this->created_at?->format(DATE_ATOM),
            'updatedAt' => $this->updated_at?->format(DATE_ATOM),
            'event' => $this->whenLoaded('event', function () {
                return [
                    'id' => $this->event->id,
                    'name' => $this->event->name,
                ];
            }),
        ];
    }
}


