<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Services\EventService;
use Illuminate\Http\Request;

class EventController extends Controller
{
    protected EventService $eventService;
    public function __construct(EventService $eventService)
    {
        $this->eventService = $eventService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Obtenemos todos los eventos y cargamos la información de la sucursal asociada
        $events =$this->eventService->get();

        return response()->json($events);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
