<?php

namespace src\admin\Events\Application\useCases;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use src\admin\Events\Domain\Contracts\EventsRepositoryInterface;
use src\admin\Events\Domain\Entities\Events;
use Throwable;

class AssignBranches
{
    public function __construct(
        private EventsRepositoryInterface $eventsRepository
    ) {}

    public function execute(array $branchIds, int $eventId, int $productId): array
    {
        try {

            $result =  [
                'message' => '',
                'data' => [],
                'success' => false,
                'status' => 200,
            ];
            
          
            [$assignedBranches, $error, $message] = $this->eventsRepository->assignBranchesProductEvent($eventId, $productId, $branchIds);
            if ($error) {
                $result['message'] = $message;
                $result['success'] = false;
                $result['status'] = 404;
                return $result;
            }
            $result['data'] = $assignedBranches;
            $result['success'] = true;
            $result['message'] = $message;
            $result['status'] = 200;
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
