<?php

/**
 * ClnkGO
 *
 * @copyright Copyright (c) 2022, BADDI Services. (https://baddi.info)
 */

namespace BADDIServices\ClnkGO\Http\Filters;

use BADDIServices\ClnkGO\Models\ScheduledPost;

class ScheduledMediaQueryFilter extends QueryFilter
{
    public function user(?string $filter = null)
    {
        if (blank($filter)) {
            return;
        }

        $this->builder->where(ScheduledPost::USER_ID_COLUMN, $filter);
    }

    public function setUser(?string $user = null): self
    {
        $this->request->merge(['user' => $user]);

        return $this;
    }
}