<?php

namespace src\admin\Events\Infrastructure\Repositories;

use src\admin\Events\Domain\Contracts\EventsRepositoryInterface;
use src\admin\Events\Domain\Entities\Events;
use App\Models\Event;
use App\Models\EventProduct;
use Illuminate\Support\Facades\Log;

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

    public function getEventById(int $id): ?Events
    {
        $foundEvent = Event::with(['city'])->where('id', '=', $id)->first();
        if($foundEvent){
            return new Events(
                $foundEvent->id,
                $foundEvent->name,
                $foundEvent->description,
                $foundEvent->start_date,
                $foundEvent->end_date,
                $foundEvent->city_id,
                $foundEvent->is_active,
                $foundEvent->created_by,
                $foundEvent->updated_by
            );
        }
        Log::debug('Evento no encontrado', ['id' => $id]);
        Log::debug('Evento encontrado', ['foundEvent' => $foundEvent]);
        return null;
    }

    public function update(Events $event): array
    {   
        try {
            $updated = Event::where('id', '=', $event->getId())->update([
                'name' => $event->getName(),
                'description' => $event->getDescription(),
                'start_date' => $event->getStartDate(),
                'end_date' => $event->getEndDate(),
                'city_id' => $event->getCityId(),
                'is_active' => $event->getIsActive(),
                'updated_by' => $event->getUpdatedById(),
            ]);
            
            if($updated){
                return [$event,false,  'Evento actualizado correctamente'];
            }
            return [null,true, 'No se pudo actualizar el evento'];
        } catch (\Throwable $th) {
            return [null,true,  $th->getMessage()];
        }
       
    }

    public function getAll(): array
    {
        $foundEvents = Event::with(['city'])->get()->toArray();
       
        return $foundEvents;
    }
}
