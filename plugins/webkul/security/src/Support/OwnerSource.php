<?php

namespace Webkul\Security\Support;

class OwnerSource
{
    public const KIND_COLUMN = 'column';

    public const KIND_RELATION = 'relation';

    public const KIND_PIVOT = 'pivot';

    public const KIND_FOLLOWERS = 'followers';

    protected function __construct(
        public string $kind,
        public array $params = [],
    ) {}

    public static function column(string $name): self
    {
        return new self(self::KIND_COLUMN, ['name' => $name]);
    }

    public static function relation(string $name, string $key = 'users.id'): self
    {
        return new self(self::KIND_RELATION, ['name' => $name, 'key' => $key]);
    }

    public static function pivot(string $table, string $foreignKey, string $relatedKey = 'user_id'): self
    {
        return new self(self::KIND_PIVOT, [
            'table'       => $table,
            'foreignKey'  => $foreignKey,
            'relatedKey'  => $relatedKey,
        ]);
    }

    public static function followers(): self
    {
        return new self(self::KIND_FOLLOWERS);
    }
}
