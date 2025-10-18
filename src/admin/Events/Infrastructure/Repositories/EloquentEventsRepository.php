<?php

namespace src\admin\Events\Infrastructure\Repositories;

use src\admin\Events\Domain\Contracts\EventsRepositoryInterface;
use src\admin\Events\Domain\Entities\Events;
use App\Models\Event;
use App\Models\EventProduct;

class EloquentEventsRepository implements EventsRepositoryInterface
{
    public function create(Events $event): Events
    {
        $eventData = [
            'name' => $event->getName(),
            'description' => $event->getDescription(),
            'start_date' => $event->getStartDate(),
            'end_date' => $event->getEndDate(),
            'city_id' => $event->getCityId(),
            'is_active' => $event->getIsActive(),
            'created_by' => $event->createdBy,
            'updated_by' => $event->updatedBy,
        ];

        $createdEvent = Event::create($eventData);
        
        // Convertir el modelo Eloquent de vuelta a la entidad Events
        return new Events(
            $createdEvent->id,
            $createdEvent->name,
            $createdEvent->description,
            $createdEvent->start_date,
            $createdEvent->end_date,
            $createdEvent->city_id,
            $createdEvent->is_active,
            $createdEvent->created_by,
            $createdEvent->updated_by
        );
    }

    public function getEventsActive(): array
    {
        $foundEvents = Event::with(['city'])->where('is_active', '=', 1)->where('start_date', '<=', now())->where('end_date', '>=', now())->get()->toArray();
        if($foundEvents){
            return $foundEvents;
        }
        return $foundEvents;
    }

    public function getProductsEvent(int $idEvent): array
    {
        $foundProducts = EventProduct::with(['restaurantProduct.restaurant.restaurantBranches.city', 'branchsProduct.branch', 'event'])->where('is_active', '=', 1)
        ->whereHas('event', function($query) use ($idEvent){
            $query->where('is_active', '=', 1)->where('start_date', '<=', now())->where('end_date', '>=', now())
            ->where('id', '=', $idEvent);
        })
        ->get()->toArray();
        if($foundProducts){
            return $foundProducts;
        }
        return $foundProducts;
    }


}
