<?php

namespace src\admin\Events\Application\useCases;

use Illuminate\Support\Facades\Log;
use src\admin\Events\Domain\Contracts\EventsRepositoryInterface;
use Throwable;

class GetEvents
{
    public function __construct(
        private EventsRepositoryInterface $eventsRepository
    ) {}

    public function execute(): array
    {
        try{
            $events = $this->eventsRepository->getAll();
            return [$events,false, 'Eventos obtenidos exitosamente'];
        }catch(Throwable $th){
            Log::error('Error en get events:', ['error' => $th->getMessage(),'file' => $th->getFile(),'line' => $th->getLine()]);
            return [null,true, $th->getMessage()];
        }
    }
}