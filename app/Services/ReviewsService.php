<?php

namespace App\Services;

use App\Exports\ReviewsExport;
use App\Http\Requests\Review\StoreReviewRequest;
use App\Models\EventProduct;
use App\Models\EventProductBranch;
use App\Models\Review;
use App\Sorts\RestaurantNameSort;
use App\Sorts\ProductNameSort;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;

use function Illuminate\Log\log;

class ReviewsService
{

  public function store(StoreReviewRequest $request)
  {
    $data = $request->validated();
    $data['mac'] = $data['deviceId'];

    // Check if event_product_branch_id is null and set it to a default value if necessary
    if (!isset($data['event_product_branch_id']) || $data['event_product_branch_id'] === null) {
      $eventBranch = EventProductBranch::where('event_product_id', $data['event_product_id'])->first();
      if ($eventBranch) {
        $data['event_product_branch_id'] = $eventBranch->id;
      }
    }

    // Validar que no se haya votado desde la misma IP para el mismo producto
    $vote = Review::where("event_product_id", $data['event_product_id'])
      ->where("event_product_branch_id", $data['event_product_branch_id'])
      ->where("ip", $data['ip'])
      ->first();

    if (!$vote) {
      $review = Review::create($data);
      return "Hamburguesa calificada correctamente";
    }
    return "Ya calificaste esta hamburguesa";
  }


  public function getRanking($idEvent)
  {
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
        DB::raw('COUNT(rev.id) as total_reviews'),
        DB::raw("SUM(CASE WHEN rev.comment IS NOT NULL AND rev.comment <> '' THEN 1 ELSE 0 END) as total_comments")
      )
      ->groupBy('rp.id', 'r.id', 'r.name', 'rp.name', 'rp.image_url', 'rev.event_product_id')
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

  public function getDetailRankingProduct($eventProductId)
  {
    
    $eventProduct = EventProduct::with([
      'restaurantProduct.restaurant',
    ])->find($eventProductId);

    if (!$eventProduct) {
      return null;
    }

    $product = $eventProduct->restaurantProduct;
    $restaurant = $product->restaurant;

    
    $summary = Review::selectRaw('
          COUNT(*) as total_reviews,
          SUM(CASE WHEN comment IS NOT NULL AND comment <> "" THEN 1 ELSE 0 END) as total_comments,
          SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) as five_star,
          SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) as four_star,
          SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) as three_star,
          SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) as two_star,
          SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) as one_star,
          ROUND(AVG(rating), 2) as avg_rating
      ')
      ->where('event_product_id', $eventProductId)
      ->first()->toArray();
    $summary = array_map('floatval', $summary);

    
    $reviews = Review::with([
      'eventProductBranch.branch:id,address'
    ])
      ->where('event_product_id', $eventProductId)
      ->get()
      ->map(function ($review) {
        return [
          "id" => $review->id,
          "rating" => $review->rating,
          "comment" => $review->comment,
          "date" => $review->created_at,
          "branch" => [
            "id" => $review->eventProductBranch?->branch?->id,
            "address" => $review->eventProductBranch?->branch?->address,
          ]
        ];
      });

    
    return [
      "product" => [
        "id" => $product->id,
        "name" => $product->name,
        "image_url" => $product->image_url,
        'description' => $product->description,
        "restaurant" => [
          "id" => $restaurant->id,
          "name" => $restaurant->name,
        ]
      ],
      "summary" => $summary,
      "reviews" => $reviews,
    ];
  }

  public function getRankingList(Request $request, $idEvent)
  {
    $paginate = $request->boolean('paginate', false);
    
    // Hacer joins necesarios para poder ordenar por campos de relaciones
    $baseQuery = Review::query()
      ->join('event_products as ep', 'reviews.event_product_id', '=', 'ep.id')
      ->join('restaurant_products as rp', 'ep.product_id', '=', 'rp.id')
      ->join('restaurants as r', 'rp.restaurant_id', '=', 'r.id')
      ->where('ep.event_id', $idEvent)
      ->select([
        'reviews.id',
        'reviews.event_product_id',
        'reviews.event_product_branch_id',
        'reviews.rating',
        'reviews.comment',
        'reviews.ip',
        'reviews.created_at',
      ]);
    
    $query = QueryBuilder::for($baseQuery)
      ->with([
        'eventProduct.restaurantProduct.restaurant:id,name',
        'eventProduct.restaurantProduct:id,name,restaurant_id,image_url',
      ])
      ->allowedFilters([
        AllowedFilter::callback('search', function ($query, $value) {
          $searchValue = strtolower($value);
          $query->where(function ($q) use ($searchValue) {
            $q->whereRaw('LOWER(r.name) LIKE ?', ["%{$searchValue}%"])
              ->orWhereRaw('LOWER(rp.name) LIKE ?', ["%{$searchValue}%"])
              ->orWhereRaw('LOWER(reviews.comment) LIKE ?', ["%{$searchValue}%"])
              ->orWhereRaw('LOWER(reviews.ip) LIKE ?', ["%{$searchValue}%"]);
          });
        }),
      ])
      ->allowedSorts([
        'rating',
        'created_at',
        'updated_at',
        AllowedSort::custom('restaurant_name', new RestaurantNameSort()),
        AllowedSort::custom('product_name', new ProductNameSort()),
        AllowedSort::custom('eventProduct.restaurantProduct.restaurant.name', new RestaurantNameSort()),
        AllowedSort::custom('eventProduct.restaurantProduct.name', new ProductNameSort()),
      ]);

    
    if ($paginate) {
      $rankingList = $query
        ->paginate($request->input('rows', config('app.paginate_rows')))
        ->appends($request->query())
        ->through(function ($review) {
          return $this->transformReviewData($review);
        });
    } else {
      $rankingList = $query->get()->map(function ($review) {
        return $this->transformReviewData($review);
      });
    }

    return $rankingList;
  }

  private function transformReviewData($review)
  {
    return [
      'id' => $review->id,
      'rating' => $review->rating,
      'comment' => $review->comment,
      'ip' => $review->ip,
      'created_at' => $review->created_at,
      'product_name' => $review->eventProduct?->restaurantProduct?->name,
      'product_id' => $review->eventProduct?->restaurantProduct?->id,
      'product_image' => $review->eventProduct?->restaurantProduct?->image_url,
      'restaurant_name' => $review->eventProduct?->restaurantProduct?->restaurant?->name,
      'restaurant_id' => $review->eventProduct?->restaurantProduct?->restaurant?->id,
    ];
  }


  public function exportRankingList(Request $request, $idEvent)
  {
    
    $requestData = $request->all();
    $requestData['paginate'] = false;
    $exportRequest = new Request($requestData);
    
    
    $reviews = $this->getRankingList($exportRequest, $idEvent);
    
    
    $fileName = 'ranking_reviews_event_' . $idEvent . '_' . date('Y-m-d_His') . '.xlsx';
    
    // Exportar a Excel
    return Excel::download(new ReviewsExport($reviews), $fileName);
  }
}
