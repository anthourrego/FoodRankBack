<?php

namespace App\Services;

use App\Http\Requests\Review\StoreReviewRequest;
use App\Models\Review;

class ReviewsService
{
    

    public function store(StoreReviewRequest $request){
      $data = $request->validated();
      $review = Review::create($data);
      return $review;
    }
}
