<?php

namespace src\admin\Events\Infrastructure\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use src\admin\Events\Application\useCases\GetEventActive;
use src\admin\Events\Application\useCases\GetProductsEvent;
use src\admin\Events\Application\useCases\StoreEvent;
use src\admin\Events\Infrastructure\Repositories\EloquentEventsRepository;
use src\admin\Events\Infrastructure\Validators\StoreEventRequest;
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

}