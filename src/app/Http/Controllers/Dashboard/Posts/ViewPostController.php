<?php

/**
 * ClnkGO
 *
 * @copyright Copyright (c) 2022, BADDI Services. (https://baddi.info)
 */

namespace BADDIServices\ClnkGO\Http\Controllers\Dashboard\Posts;

use Illuminate\Http\Response;
use Illuminate\Contracts\View\View;
use Illuminate\Contracts\View\Factory;
use BADDIServices\ClnkGO\Http\Controllers\DashboardController;

class ViewPostController extends DashboardController
{
    public function __invoke(string $accountId, string $locationId, string $postId): View|Factory
    {
        $post = $this->googleMyBusinessService->getBusinessLocationPost($accountId, $locationId, $postId);
        abort_if(empty($post), Response::HTTP_NOT_FOUND);

        return $this->render(
            'dashboard.posts.view',
            [
                'title' => 'View post',
                'post'  => $post,
            ]
        );
    }
}