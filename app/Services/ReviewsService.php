<?php

namespace App\Services;

use App\Http\Requests\Review\StoreReviewRequest;
use App\Models\EventProductBranch;
use App\Models\Review;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use function Illuminate\Log\log;

class ReviewsService
{

	public function store(StoreReviewRequest $request){
		$data = $request->validated();
		$data['mac'] = $data['deviceId'];

        // Check if event_product_branch_id is null and set it to a default value if necessary
        if (!isset($data['event_product_branch_id']) || $data['event_product_branch_id'] === null) {
            $eventBranch = EventProductBranch::where('event_product_id', $data['event_product_id'])->first();
            if ($eventBranch) {
                $data['event_product_branch_id'] = $eventBranch->id;
            }
        }

		$vote = Review::where("event_product_id", $data['event_product_id'])->where("event_product_branch_id", $data['event_product_branch_id'])->where("ip", $data['ip'])->where("mac", $data['mac'])->first();

		if (!$vote) {
			$review = Review::create($data);
			return "Hamburguesa calificada correctamente";
	    }
	    return "Ya calificaste esta hamburguesa";
	}


	public function getRanking($idEvent){
		$globalAvg = DB::table('reviews')->avg('rating');
		$minVotes  = 10; // mínimo de votos para ponderar

		$rankingRestaurants = DB::table('reviews as rev')
    ->join('event_products as ep', 'rev.event_product_id', '=', 'ep.id')
    ->join('restaurant_products as rp', 'ep.product_id', '=', 'rp.id')
    ->join('restaurants as r', 'rp.restaurant_id', '=', 'r.id')
    ->select(
        'r.id as restaurant_id',
        'r.name as restaurant_name',
				'rp.name as product_name',
				'rp.image_url as product_image',
				'rp.id as product_id',
				'rev.event_product_id',
        DB::raw('ROUND(AVG(rev.rating), 2) as avg_rating'),
        DB::raw('COUNT(rev.id) as total_reviews')
    )
    ->groupBy('rp.id','r.id', 'r.name', 'rp.name', 'rp.image_url','rev.event_product_id')
    ->where('ep.event_id', '=', $idEvent)
		->orderByDesc(DB::raw('AVG(rev.rating)'))
    ->orderByDesc(DB::raw('COUNT(rev.id)'))
    ->get()
   /*  ->map(function($item) use ($globalAvg, $minVotes) {
        $v = $item->total_reviews;   // votos del restaurante
        $R = $item->avg_rating;   // promedio del restaurante
        $C = $globalAvg; // promedio global
        $m = $minVotes;  // mínimo requerido

        // Ranking bayesiano
        $item->ranking_score = (($v / ($v + $m)) * $R) + (($m / ($v + $m)) * $C);
        return $item;
    })
    ->sortByDesc('ranking_score') */
		->map(function ($item) {
				$item->avg_rating = (float) $item->avg_rating;
				$item->total_reviews = (int) $item->total_reviews;
				// Obtener el detalle de calificaciones (1 a 5 estrellas)
        $ratings = Review::select('rating', DB::raw('COUNT(*) as total'))
            ->where('event_product_id', $item->event_product_id)
            ->groupBy('rating')
            ->pluck('total', 'rating')
            ->toArray();

        // Asegurar que existan todas las estrellas del 1 al 5
        $detail = [];
        foreach (range(1, 5) as $star) {
            $detail[$star] = $ratings[$star] ?? 0;
        }

        $item->detail = $detail;

        
				return $item;
		});
    /* ->values() */
    

		return $rankingRestaurants;
	}
}
