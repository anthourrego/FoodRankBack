<?php

namespace src\admin\Events\Application\useCases;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use src\admin\Events\Domain\Contracts\EventsRepositoryInterface;
use src\admin\Events\Domain\Entities\Events;
use Throwable;

class StoreEvent
{
    public function __construct(
        private EventsRepositoryInterface $eventsRepository
    ) {}

    public function execute(array $data): array
    {
        try {

            $result =  [
                'message' => '',
                'data' => [],
                'success' => false,
                'status' => 200,
            ];
            $userId = Auth::user()->id ?? null;
            $event = new Events(
                null,
                $data['name'],
                $data['description'],
                $data['start_date'],
                $data['end_date'],
                $data['city_id'],
                true,
                $userId,
                $userId,

            );
            $storedEvent = $this->eventsRepository->create($event);
            if ($storedEvent) {
                $result['data'] = $storedEvent;
                $result['success'] = true;
                $result['message'] = 'Evento almacenado';
            } else {
                $result['message'] = 'No se logro almacenar el evento';
                $result['success'] = false;
                $result['status'] = 404;
            }
            return $result;
        } catch (Throwable $th) {
            Log::error($th->getMessage(), [
                'file' => $th->getFile(),
                'line' => $th->getLine(),
                'trace' => $th->getTraceAsString(),
            ]);
            $result['message'] = $th->getMessage();
            $result['success'] = false;
            $result['status'] = 500;
            return $result;
        }
    }
}
