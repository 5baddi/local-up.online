<?php

/**
 * ClnkGO
 *
 * @copyright Copyright (c) 2022, BADDI Services. (https://baddi.info)
 */

namespace BADDIServices\ClnkGO\Http\Controllers\Dashboard\Media;

use Illuminate\Http\Response;
use Illuminate\Contracts\View\View;
use App\Http\Requests\PaginationRequest;
use BADDIServices\ClnkGO\Http\Controllers\DashboardController;

class IndexController extends DashboardController
{
    public function __invoke(PaginationRequest $request): View|Response
    {
        $media = $this->googleMyBusinessService->getBusinessLocationMedia($request->query('next'));

        if ($request->has('next')) {
            return response(
                    $this->render(
                        'dashboard.media.partials.gallery',
                        [
                            'media' => $media,
                        ]
                    )
                )
                ->withHeaders([
                    'Gmb-Next' => $accountLocations['nextPageToken'] ?? null
                ]);
        }

        return $this->render(
            'dashboard.media.index',
            [
                'title' => trans('global.media'),
                'media' => $media,
            ]
        );
    }
}