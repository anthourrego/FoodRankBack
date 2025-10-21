<?php

namespace src\admin\Events\Infrastructure\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use src\admin\Events\Application\useCases\AssignBranches;
use src\admin\Events\Application\useCases\DeleteProductEvent;
use src\admin\Events\Application\useCases\GetEventActive;
use src\admin\Events\Application\useCases\GetEvents;
use src\admin\Events\Application\useCases\GetProductsEvent;
use src\admin\Events\Application\useCases\StoreEvent;
use src\admin\Events\Application\useCases\StoreProductEvent;
use src\admin\Events\Application\useCases\UpdateEvent;
use src\admin\Events\Infrastructure\Repositories\EloquentEventsRepository;
use src\admin\Events\Infrastructure\Validators\StoreBranchesProductEventRequest;
use src\admin\Events\Infrastructure\Validators\StoreEventRequest;
use src\admin\Events\Infrastructure\Validators\StoreProductEventRequest;
use src\admin\Events\Infrastructure\Validators\UpdateEventRequest;
use Throwable;

final class EventsController extends Controller { 
    use ApiResponseTrait;
    public function __construct(private EloquentEventsRepository $eventsRepository)
    {
        $this->eventsRepository = $eventsRepository;
    }

    public function getEventsActive() { 
        $response = (object) [
            'message' => '',
            'data' => [],
            'success' => false,
            'status' => 200,
        ];

        try {
            $events = new GetEventActive($this->eventsRepository);
            $eventsFound = $events->execute();

            $response->data = $eventsFound;
            $response->success = true;
            $response->message = 'Eventos activos obtenidos exitosamente';
        } catch (\Exception $e) {
            Log::error($e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            $response->message = 'Error al obtener los eventos activos';
            $response->success = false;
            $response->status = 500;
        }finally {

            return response()->json($response,$response->status);
        }

    }

    public function getProductsEvent(Request $request, $idEvent=0) { 
        $idEvent = $idEvent;
        $response = (object) [
            'message' => '',
            'data' => [],
            'success' => false,
            'status' => 200,
        ];
        if($idEvent == 0) {
            $response->message = 'El id del evento es requerido';
            $response->success = false;
            $response->status = 400;
            return response()->json($response,$response->status);
        }

        try {
            $products = new GetProductsEvent($this->eventsRepository);
            $productsFound = $products->execute($idEvent);
    
            $response->data = $productsFound['data'];
            $response->success = $productsFound['success'];
            $response->message = $productsFound['message'];
            $response->status = $productsFound['status'];
    
        } catch (\Exception $e) {
            Log::error($e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            $response->message = 'Error al obtener los productos de eventos';
            $response->success = false;
            $response->status = 500;
        }finally {
            return response()->json($response,$response->status);
        }
        
    }

    public function store(StoreEventRequest $request){
        try{
            
            
            
            $storeEvent = new StoreEvent($this->eventsRepository);
            $eventResult = $storeEvent->execute($request->validated());
            if($eventResult['success']){
                return $this->successResponse($eventResult['message'],$eventResult['data']);
            }else{
                return $this->errorResponse($eventResult['message']);
            }
            
        }catch(Throwable $th){
            Log::error('Error en store event:', ['error' => $th->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => $th->getMessage() || 'Se presento un error almacenado el evento',
            ], 500);
        }
    }

    public function update(UpdateEventRequest $request, $id){
        try{
            $updateEvent = new UpdateEvent($this->eventsRepository);
            $eventResult = $updateEvent->execute($request->validated(), $id);
            if($eventResult['success']){
                return $this->successResponse($eventResult['message'],$eventResult['data']);
            }else{
                return $this->errorResponse($eventResult['message'],$eventResult['data'],$eventResult['status']);
            }
        }catch(Throwable $th){
            Log::error('Error en update event:', ['error' => $th->getMessage()]);
            return $this->errorResponse($th->getMessage() || 'Se presento un error al actualizar el evento',[],500);
        }
    }

    public function index(){
        try{
            $events = new GetEvents($this->eventsRepository);
            [$eventsFound, $error, $message] = $events->execute();
            if($error){
                return $this->errorResponse($message);
            }
            return $this->successResponse($message, $eventsFound);
        }catch(Throwable $th){
            Log::error('Error en index event:', ['error' => $th->getMessage(),'file' => $th->getFile(),'line' => $th->getLine()]);
            return $this->errorResponse($th->getMessage() || 'Se presento un error al obtener los eventos',[],500);
        }
    }   

    public function storeProductsEvent(StoreProductEventRequest $request, $eventId){
        try {
            $storeProductEvent = new StoreProductEvent($this->eventsRepository);
            $productEventResult = $storeProductEvent->execute($request->validated(), $eventId);
            if($productEventResult['success']){
                return $this->successResponse($productEventResult['message'],$productEventResult['data']);
            }else{
                return $this->errorResponse($productEventResult['message'],$productEventResult['data'],$productEventResult['status']);
            }
        }catch(Throwable $th){
            Log::error('Error en store products event:', ['error' => $th->getMessage(),'file' => $th->getFile(),'line' => $th->getLine()]);
            return $this->errorResponse($th->getMessage() || 'Se presento un error al almacenar los productos del evento',[],500);
        }
    }

    public function deleteProductEvent(Request $request, $eventId, $productId){
        try {
            $deleteProductEvent = new DeleteProductEvent($this->eventsRepository);
            $productEventResult = $deleteProductEvent->execute($eventId, $productId);
            if($productEventResult['success']){
                return $this->successResponse($productEventResult['message'],$productEventResult['data']);
            }else{
                return $this->errorResponse($productEventResult['message'],$productEventResult['data'],$productEventResult['status']);
            }
        }catch(Throwable $th){
            Log::error('Error en delete products event:', ['error' => $th->getMessage(),'file' => $th->getFile(),'line' => $th->getLine()]);
            return $this->errorResponse($th->getMessage() || 'Se presento un error al eliminar los productos del evento',[],500);
        }
    }

    public function assignedBranch(StoreBranchesProductEventRequest $request, $eventId, $productId){
        try{
            $assignedBranch = new AssignBranches($this->eventsRepository);
            $assignedBranchResult = $assignedBranch->execute($request->validated(), $eventId, $productId);
            if($assignedBranchResult['success']){
                return $this->successResponse($assignedBranchResult['message'],$assignedBranchResult['data']);
            }else{
                return $this->errorResponse($assignedBranchResult['message'],$assignedBranchResult['data'],$assignedBranchResult['status']);
            }
        }catch(Throwable $th){
            Log::error('Error en assigned branch:', ['error' => $th->getMessage(),'file' => $th->getFile(),'line' => $th->getLine()]);
            return $this->errorResponse($th->getMessage() || 'Se presento un error al asignar las sucursales al producto del evento',[],500);
        }
    }
}