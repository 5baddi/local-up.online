<?php

/**
 * ClnkGO
 *
 * @copyright Copyright (c) 2022, BADDI Services. (https://baddi.info)
 */

namespace BADDIServices\ClnkGO\Entities;

use JsonSerializable;

class ArrayValue implements JsonSerializable
{
    public array $array;

    public function __construct(array $array)
    {
        $this->array = $array;
    }

    public function jsonSerialize(): array
    {
        return $this->array;
    }
}
