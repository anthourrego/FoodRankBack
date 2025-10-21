<?php

namespace src\admin\Events\Domain\Contracts;

use src\admin\Events\Domain\Entities\Events;

interface EventsRepositoryInterface
{
    public function create(Events $event): Events;
    public function getEventsActive(): array;
    public function getProductsEvent(int $idEvent): array;
    public function getEventById(int $id): ?Events;

    public function update(Events $event): array;
    public function getAll(): array;
    public function createProductEvent(int $eventId, int $productId): array;
    public function deleteProductEvent(int $eventId, int $productId): array;
    public function assignBranchesProductEvent(int $eventId, int $productId, array $branchIds): array;
  /*   public function update(Event $event): Event;
    public function delete(Event $event): bool;
    public function findById(int $id): Event;
    public function findAll(): array; */
}


