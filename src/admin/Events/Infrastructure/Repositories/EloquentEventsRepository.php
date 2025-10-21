<?php

namespace src\admin\Events\Infrastructure\Repositories;

use src\admin\Events\Domain\Contracts\EventsRepositoryInterface;
use src\admin\Events\Domain\Entities\Events;
use App\Models\Event;
use App\Models\EventProduct;
use App\Models\EventProductBranch;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

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
            'created_by' => $event->getCreatedById(),
            'updated_by' => $event->getUpdatedById(),
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
        if ($foundEvents) {
            return $foundEvents;
        }
        return $foundEvents;
    }

    public function getProductsEvent(int $idEvent): array
    {
        $foundProducts = EventProduct::with(['restaurantProduct.restaurant.restaurantBranches.city', 'branchsProduct.branch', 'event'])->where('is_active', '=', 1)
            ->whereHas('event', function ($query) use ($idEvent) {
                $query->where('is_active', '=', 1)->where('start_date', '<=', now())->where('end_date', '>=', now())
                    ->where('id', '=', $idEvent);
            })
            ->get()->toArray();
        if ($foundProducts) {
            return $foundProducts;
        }
        return $foundProducts;
    }

    public function getEventById(int $id): ?Events
    {
        $foundEvent = Event::with(['city'])->where('id', '=', $id)->first();
        if ($foundEvent) {

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

            if ($updated) {
                return [$event, false,  'Evento actualizado correctamente'];
            }
            return [null, true, 'No se pudo actualizar el evento'];
        } catch (\Throwable $th) {
            return [null, true,  $th->getMessage()];
        }
    }

    public function getAll(): array
    {
        $foundEvents = Event::with(['city'])->get()->toArray();

        return $foundEvents;
    }

    public function createProductEvent(int $eventId, int $productId): array
    {
        //agregar validacion de no agregar el mismo producto al mismo evento
        $productEvent = EventProduct::where('event_id', '=', $eventId)->where('product_id', '=', $productId)->first();
        if ($productEvent) {
            return [null, true, 'El producto ya esta agregado al evento'];
        }

        $productEvent = EventProduct::create([
            'event_id' => $eventId,
            'product_id' => $productId,
            'is_active' => true,
            'created_by' => Auth::user()->id,
        ]);
        if ($productEvent) {
            return [$productEvent, false, 'Producto del evento almacenado correctamente'];
        }
        return [null, true, 'No se pudo almacenar el producto del evento'];
    }

    public function deleteProductEvent(int $eventId, int $productId): array
    {
        $productEvent = EventProduct::where('event_id', '=', $eventId)->where('product_id', '=', $productId)->first();
        if ($productEvent) {
            $productEvent->delete();
            return [$productEvent, false, 'Producto del evento eliminado correctamente'];
        }
        return [null, true, 'No se pudo eliminar el producto del evento'];
    }

    public function assignBranchesProductEvent(int $eventId, int $productId, array $branchIds): array
    {
        //se envia un array de ids de sucursales, si en la db hay registros que no coincidan con los ids enviados, se deben eliminar, y asignar los nuevos
        try {
            
        foreach ($branchIds['branch_ids'] as $branchId) {
                
                //desactivar todas las sucursales del producto
                EventProductBranch::where('event_product_id', '=', $eventId)
                ->update([
                    'is_active' => false,
                    'updated_by' => Auth::user()->id,
                ]);
                $assignedBranch = EventProductBranch::where('event_product_id', '=', $eventId)->where('restaurant_branch_id', '=', $branchId)->first();
                if ($assignedBranch) {
                    $assignedBranch->update([
                        'is_active' => true,
                        'updated_by' => Auth::user()->id,
                    ]);
                } else {
                    $assignedBranch = EventProductBranch::create([
                        'event_product_id' => $eventId,
                        'restaurant_branch_id' => $branchId,
                        'is_active' => true,
                        'created_by' => Auth::user()->id,
                        'updated_by' => Auth::user()->id,
                    ]);
                }
                
            }
        } catch (Throwable $th) {
            return [null, true, $th->getMessage()];
        }
        return [null, false, 'Sucursales asignadas correctamente'];
    }
}
