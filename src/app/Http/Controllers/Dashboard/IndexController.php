<?php

/**
 * ClnkGO
 *
 * @copyright Copyright (c) 2022, BADDI Services. (https://baddi.info)
 */

namespace BADDIServices\ClnkGO\Http\Controllers\Dashboard;

use Illuminate\Http\Response;
use Illuminate\Contracts\View\View;
use App\Http\Requests\PaginationRequest;
use BADDIServices\ClnkGO\Http\Controllers\DashboardController;

class IndexController extends DashboardController
{
    public function __invoke(PaginationRequest $request): View|Response
    {
        $posts = $this->googleMyBusinessService->getBusinessLocationPosts($request->query('next'));

        if ($request->has('next')) {
            return response(
                $this->render(
                        'dashboard.posts.partials.gallery',
                        [
                            'posts' => $posts,
                        ]
                    )
                )
                ->withHeaders([
                    'Gmb-Next' => $posts['nextPageToken'] ?? null
                ]);
        }

        return $this->render(
            'dashboard.posts.index',
            [
                'title'         => trans('global.dashboard'),
                'posts'         => $posts,
            ]
        );
    }
}