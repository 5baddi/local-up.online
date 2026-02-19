<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use BADDIServices\ClnkGO\Models\Authenticatable;
use BADDIServices\ClnkGO\Traits\Filterable;
use BADDIServices\ClnkGO\Models\UserGoogleCredentials;

/**
 * @property UserGoogleCredentials|null $googleCredentials
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable, Filterable;

    public const string EMAIL_COLUMN = 'email';
    public const string LAST_NAME_COLUMN = 'last_name';
    public const string FIRST_NAME_COLUMN = 'first_name';
    public const string PHONE_COLUMN = 'phone';
    public const string PASSWORD_COLUMN = 'password';
    public const string KEYWORDS_COLUMN = 'keywords';
    public const string CUSTOMER_ID_COLUMN = 'customer_id';
    public const string LAST_LOGIN_COLUMN = 'last_login';
    public const string VERIFIED_AT_COLUMN = 'verified_at';
    public const string CONFIRMATION_TOKEN_COLUMN = 'confirmation_token';
    public const string REMEMBER_TOLEN_COLUMN = 'remember_token';
    public const string ROLE_COLUMN = 'role';
    public const string IS_SUPERADMIN_COLUMN = 'is_superadmin';
    public const string BANNED_COLUMN = 'banned';

    public const string DEFAULT_ROLE = 'client';

    public const array ROLES = [
        self::DEFAULT_ROLE,
    ];

    /** @var array */
    protected $guarded = [];

    /** @var array */
    protected $hidden = [
        self::PASSWORD_COLUMN,
        self::REMEMBER_TOLEN_COLUMN,
    ];

    /** @var array */
    protected $casts = [
        self::CREATED_AT                => 'datetime',
        self::UPDATED_AT                => 'datetime',
        self::LAST_LOGIN_COLUMN         => 'datetime',
        self::VERIFIED_AT_COLUMN        => 'datetime',
        self::IS_SUPERADMIN_COLUMN      => 'boolean',
        self::BANNED_COLUMN             => 'boolean',
    ];

    public function setEmailAttribute($value): self
    {
        $this->attributes[self::EMAIL_COLUMN] = strtolower($value);

        return $this;
    }

    public function getEmail(): string
    {
        return $this->getAttribute(self::EMAIL_COLUMN);
    }
    
    public function getConfirmationToken(): ?string
    {
        return $this->getAttribute(self::CONFIRMATION_TOKEN_COLUMN);
    }
    
    public function getFirstName(): string
    {
        return $this->getAttribute(self::FIRST_NAME_COLUMN);
    }

    public function getFullName(): ?string
    {
        return ucwords($this->getAttribute(self::FIRST_NAME_COLUMN) . ' ' . $this->getAttribute(self::LAST_NAME_COLUMN));
    }

    public function isSuperAdmin(): bool
    {
        return $this->getAttribute(self::IS_SUPERADMIN_COLUMN) === true && is_null($this->getAttribute(self::ROLE_COLUMN));
    }
    
    public function isBanned(): bool
    {
        return $this->getAttribute(self::BANNED_COLUMN) === true;
    }
    
    public function isEmailConfirmed(): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        return is_null($this->getAttribute(self::CONFIRMATION_TOKEN_COLUMN))
            && ! is_null($this->getAttribute(self::VERIFIED_AT_COLUMN));
    }

    public function hasPassword(): bool
    {
        return $this->getAttribute(self::PASSWORD_COLUMN) !== null;
    }
    
    public function getPassword(): ?string
    {
        return $this->getAttribute(self::PASSWORD_COLUMN);
    }

    public function getKeywordsAsString(): ?string
    {
        return $this->getAttribute(self::KEYWORDS_COLUMN);
    }
    
    public function getKeywords(): array
    {
        if ($this->getAttribute(self::KEYWORDS_COLUMN) !== null && strlen($this->getAttribute(self::KEYWORDS_COLUMN)) > 0) {
            return explode(',', $this->getAttribute(self::KEYWORDS_COLUMN));
        }

        return [];
    }

    public function googleCredentials(): HasOne
    {
        return $this->hasOne(UserGoogleCredentials::class, UserGoogleCredentials::USER_ID_COLUMN);
    }

    public function isGoogleAccountAuthenticated(): bool
    {
        return $this->googleCredentials instanceof UserGoogleCredentials && ! $this->googleCredentials->isExpired();
    }
}