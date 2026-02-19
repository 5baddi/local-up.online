<?php

/**
 * ClnkGO
 *
 * @copyright Copyright (c) 2022, BADDI Services. (https://baddi.info)
 */

namespace BADDIServices\ClnkGO\Repositories;

use BADDIServices\ClnkGO\App;
use BADDIServices\ClnkGO\Models\ScheduledPost;
use Illuminate\Pagination\LengthAwarePaginator;
use BADDIServices\ClnkGO\Http\Filters\QueryFilter;

class ScheduledPostRepository
{
    public function paginate(QueryFilter $queryFilter): LengthAwarePaginator
    {
        return ScheduledPost::query()
            ->with(['media'])
            ->filter($queryFilter)
            ->paginate(App::PAGINATION_LIMIT, ['*'], 'page', $queryFilter->getPage());
    }
}