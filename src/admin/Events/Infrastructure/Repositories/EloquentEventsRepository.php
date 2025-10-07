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
        return Event::create($event);
    }

    public function getEventsActive(): array
    {
        $foundEvents = Event::with(['city','eventProducts'])->where('is_active', '=', 1)->where('start_date', '<=', now())->where('end_date', '>=', now())->get()->toArray();
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
