<?php

/**
 * ClnkGO
 *
 * @copyright Copyright (c) 2022, BADDI Services. (https://baddi.info)
 */

namespace BADDIServices\ClnkGO\Http\Controllers\Dashboard\Reviews;

use Illuminate\Http\Response;
use Illuminate\Contracts\View\View;
use Illuminate\Contracts\View\Factory;
use BADDIServices\ClnkGO\Http\Controllers\DashboardController;

class ViewReviewController extends DashboardController
{
    public function __invoke(string $id): View|Factory
    {
        $review = $this->googleMyBusinessService->getBusinessLocationReview($id);
        abort_if(empty($review), Response::HTTP_NOT_FOUND);

        return $this->render(
            'dashboard.reviews.view',
            [
                'title'     => trans('global.view_review'),
                'reviewId'  => $id,
                'review'    => $review,
            ]
        );
    }
}