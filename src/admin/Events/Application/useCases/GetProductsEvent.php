<?php

namespace src\admin\Events\Application\useCases;

use src\admin\Events\Domain\Contracts\EventsRepositoryInterface;

class GetProductsEvent
{
    public function __construct(
        private EventsRepositoryInterface $eventsRepository
    ) {}

    public function execute(int $idEvent): array
    {   
        $result =  [
            'message' => '',
            'data' => [],
            'success' => false,
            'status' => 200,
        ];
        $foundProducts = $this->eventsRepository->getProductsEvent($idEvent);
        if($foundProducts){
            $result['data'] = $foundProducts;
            $result['success'] = true;
            $result['message'] = 'Productos de eventos obtenidos exitosamente';
        }else{
            $result['message'] = 'No se encontraron productos de eventos';
            $result['success'] = false;
            $result['status'] = 404;
        }
        return $result;
    }
}