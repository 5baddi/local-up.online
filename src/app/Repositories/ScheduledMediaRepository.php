<?php

/**
 * ClnkGO
 *
 * @copyright Copyright (c) 2022, BADDI Services. (https://baddi.info)
 */

namespace BADDIServices\ClnkGO\Repositories;

use BADDIServices\ClnkGO\App;
use BADDIServices\ClnkGO\Models\ScheduledMedia;
use Illuminate\Pagination\LengthAwarePaginator;
use BADDIServices\ClnkGO\Http\Filters\QueryFilter;

class ScheduledMediaRepository
{
    public function paginate(QueryFilter $queryFilter): LengthAwarePaginator
    {
        return ScheduledMedia::query()
            ->filter($queryFilter)
            ->paginate(App::PAGINATION_LIMIT, ['*'], 'page', $queryFilter->getPage());
    }
}