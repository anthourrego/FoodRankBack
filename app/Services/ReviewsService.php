<?php

namespace App\Services;

use App\Http\Requests\Review\StoreReviewRequest;
use App\Models\Review;

class ReviewsService
{

	public function store(StoreReviewRequest $request){
		$data = $request->validated();
		$data['mac'] = $data['deviceId'];
		$vote = Review::where("event_product_id", $data['event_product_id'])->where("event_product_branch_id", $data['event_product_branch_id'])->where("ip", $data['ip'])->where("mac", $data['mac'])->first();

		if (!$vote) {
			$review = Review::create($data);
			return "Hamburguesa calificada correctamente";
	    }
	    return "Ya calificaste esta hamburguesa";
	}
}
