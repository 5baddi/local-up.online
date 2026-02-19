<?php

/**
 * ClnkGO
 *
 * @copyright Copyright (c) 2022, BADDI Services. (https://baddi.info)
 */

namespace BADDIServices\ClnkGO\Http\Controllers\Dashboard\Reviews;

use Illuminate\Support\Arr;
use Illuminate\Http\Response;
use Illuminate\Contracts\View\View;
use App\Http\Requests\PaginatedReviewsRequest;
use BADDIServices\ClnkGO\Http\Controllers\DashboardController;

class IndexController extends DashboardController
{
    public function __invoke(PaginatedReviewsRequest $request): View|Response
    {
        $reviews = $this->googleMyBusinessService->getBusinessLocationReviews($request->query('next'));
        $hasReplies = $request->boolean('has_replies');

        $reviews['reviews'] = array_filter($reviews['reviews'] ?? [], function ($review) use ($hasReplies) {
            return $hasReplies
                ? ! empty($review['reviewReply'] ?? null)
                : empty($review['reviewReply'] ?? null);
        });

        if ($request->has('next')) {
            return response(
                    $this->render(
                        'dashboard.reviews.partials.list',
                        [
                            'reviews' => $reviews,
                        ]
                    )
                )
                ->withHeaders([
                    'Gmb-Next' => $reviews['nextPageToken'] ?? null
                ]);
        }

        return $this->render(
            'dashboard.reviews.index',
            [
                'title'     => trans('global.reviews'),
                'reviews'   => $reviews,
            ]
        );
    }
}