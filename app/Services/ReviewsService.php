<?php

namespace App\Services;

use App\Http\Requests\Review\StoreReviewRequest;
use App\Models\EventProductBranch;
use App\Models\Review;

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
}
