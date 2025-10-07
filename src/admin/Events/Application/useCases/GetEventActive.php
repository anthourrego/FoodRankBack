<?php

namespace src\admin\Events\Application\useCases;

use src\admin\Events\Domain\Contracts\EventsRepositoryInterface;

class GetEventActive
{
    public function __construct(
        private EventsRepositoryInterface $eventsRepository
    ) {}

    public function execute(): array
    {
        return $this->eventsRepository->getEventsActive();
    }
}