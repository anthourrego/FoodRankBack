<?php

namespace src\admin\Events\Application\useCases;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use src\admin\Events\Domain\Contracts\EventsRepositoryInterface;
use src\admin\Events\Domain\Entities\Events;
use Throwable;

class UpdateEvent
{
    public function __construct(
        private EventsRepositoryInterface $eventsRepository
    ) {}

    public function execute(array $data, int $id): array
    {
        try {

            $result =  [
                'message' => '',
                'data' => [],
                'success' => false,
                'status' => 200,
            ];
            $userId = Auth::user()->id ?? null;
            
            $foundEvent = $this->eventsRepository->getEventById($id);
            if(!$foundEvent){
                $result['message'] = 'Evento no encontrado';
                $result['success'] = false;
                $result['status'] = 404;
                return $result;
            }
            
            $event = new Events(
                $foundEvent->id,
                $data['name'],
                $data['description'],
                $data['start_date'],
                $data['end_date'],
                $data['city_id'],
                $data['is_active'],
                $userId,
                $userId,
            );
        
            [$storedEvent, $error, $message] = $this->eventsRepository->update($event);
            if ($storedEvent) {
                $result['data'] = $storedEvent;
                $result['success'] = true;
                $result['message'] = $message ?? '';
            } else {
                $result['message'] = $message;
                $result['success'] = false;
                $result['status'] = $error ? 400 : 404;
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
