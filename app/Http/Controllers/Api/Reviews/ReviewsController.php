<?php


namespace App\Http\Controllers\Api\Reviews;

use App\Http\Controllers\Controller;
use App\Http\Requests\Review\StoreReviewRequest;
use App\Services\ReviewsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class ReviewsController extends Controller
{
    protected ReviewsService $reviewsService;
    public function __construct(ReviewsService $reviewsService)
    {
        $this->reviewsService = $reviewsService;
    }


    public function store(StoreReviewRequest $request):JsonResponse
    {

        $restaurantProduct = $this->reviewsService->store($request);
        if($restaurantProduct){
            return response()->json(['message' => $restaurantProduct],201);
        }
        return response()->json(['message' => 'Review not created'],400);
    }

    public function getRanking($idEvent):JsonResponse
    {
        $ranking = $this->reviewsService->getRanking($idEvent);
        return response()->json($ranking,200);
    }

    public function getDetailRankingProduct($event_product_id):JsonResponse
    {
        $detailRanking = $this->reviewsService->getDetailRankingProduct($event_product_id);
        return response()->json($detailRanking,200);
    }

}
