<?php

/**
 * ClnkGO
 *
 * @copyright Copyright (c) 2022, BADDI Services. (https://baddi.info)
 */

namespace BADDIServices\ClnkGO\Models;

use BADDIServices\ClnkGO\Entities\ModelEntity;

class AccountLocation extends ModelEntity
{
    public const string USER_ID_COLUMN = 'user_id';
    public const string ACCOUNT_ID_COLUMN = 'account_id';
    public const string LOCATION_ID_COLUMN = 'location_id';
    public const string TITLE_COLUMN = 'title';
    public const string DESCRIPTION_COLUMN = 'description';

    public function getUserId(): string
    {
        return (string) $this->getAttribute(self::USER_ID_COLUMN);
    }

    public function getAccountId(): string
    {
        return (string) $this->getAttribute(self::ACCOUNT_ID_COLUMN);
    }
    public function getLocationId(): string
    {
        return (string) $this->getAttribute(self::LOCATION_ID_COLUMN);
    }

    public function getTitle(): string
    {
        return (string) $this->getAttribute(self::TITLE_COLUMN);
    }

    public function getDescription(): ?string
    {
        return $this->getAttribute(self::DESCRIPTION_COLUMN) ?? null;
    }
}