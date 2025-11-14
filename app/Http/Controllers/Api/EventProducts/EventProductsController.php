<?php


namespace App\Http\Controllers\Api\EventProducts;

use App\Http\Controllers\Controller;
use App\Models\EventProduct;
use App\Models\RestaurantProduct;
use App\Services\EventProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;


class EventProductsController extends Controller
{
    protected EventProductService $eventProductService;
    public function __construct(EventProductService $eventProductService)
    {
        $this->eventProductService = $eventProductService;
    }


    public function index(Request $request):JsonResponse
    {

        $restaurantProduct = $this->eventProductService->get();
        return response()->json($restaurantProduct,200);
    }

    public function findEventProduct(Request $request):JsonResponse
    {
        $idProduct = $request->get('idProduct') ?? 0;
        $idEvent = $request->get('idEvent') ?? 0;
        $idBranch = $request->get('idBranch') ?? 0;
        $restaurantProduct = $this->eventProductService->filter($idEvent,$idProduct,$idBranch);
        return response()->json($restaurantProduct,200);
    }

    public function showImage($imagen = null)
    {
        // 1. Define la ruta de la imagen por defecto.
        $defaultImagePath = public_path('images/nofoto.png');

        if ($imagen && Storage::disk("public")->exists("images/products/{$imagen}")) {
            // Si existe, devuelve la imagen del producto.
            return Storage::disk('public')->response("images/products/{$imagen}");
        }

        // 4. Si no, devuelve la imagen por defecto.
        return response()->file($defaultImagePath);
    }

}
