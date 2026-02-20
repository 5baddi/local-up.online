<?php

/**
 * ClnkGO
 *
 * @copyright Copyright (c) 2022, BADDI Services. (https://baddi.info)
 */

namespace BADDIServices\ClnkGO\Http\Filters;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

abstract class QueryFilter
{
    public const SORT_FIELD = "-created_at";

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Builder
     */
    protected $builder;

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param Builder $builder
     */
    public function apply(Builder $builder)
    {
        $this->builder = $builder;

        foreach ($this->fields() as $field => $value) {
            $method = Str::camel($field);
            if (method_exists($this, $method)) {
                call_user_func_array([$this, $method], (array)$value);
            }
        }

        if (is_null($this->getSortField())) {
            $this->sort();
        }
    }

    /**
     * Sort the collection by the sort field
     * Examples: sort= title,-status || sort=-title || sort=status
     *
     * @param string $value
     */
    public function sort(?string $value = null)
    {
        collect(explode(',', (! blank($value) ? $value : $this->getDefaultSortField())))->mapWithKeys(function (string $field) {
            switch (substr($field, 0, 1)) {
                case '-':
                    return [substr($field, 1) => 'desc'];
                case ' ':
                    return [substr($field, 1) => 'asc'];
                default:
                    return [$field => 'asc'];
            }
        })->each(function (string $order, string $field) {
            $this->builder->orderBy($field, $order);
        });
    }

    public function getPage(): int
    {
        return $this->request->query("page") ?? 1;
    }
    
    public function setPage(?int $page = null): self
    {
        $this->request->merge(["page" => $page]);

        return $this;
    }
    
    public function getSortField(): ?string
    {
        return $this->request->query("sort");
    }
    
    public function getDefaultSortField(): string
    {
        return self::SORT_FIELD;
    }

    /**
     * @return array
     */
    protected function fields(): array
    {
        return array_filter(
            array_map(function ($value) {
                if (is_string($value)) {
                    return trim($value);
                }

                return $value;
            }, $this->request->all())
        );
    }
}