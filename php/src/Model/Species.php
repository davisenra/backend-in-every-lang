<?php

declare(strict_types=1);

namespace App\Model;

final readonly class Species
{
    public function __construct(
        public int $id,
        public string $name,
    ) {}

    /**
     * @param array<string, mixed> $row
     */
    public static function fromDatabaseRow(array $row): self
    {
        return new self(
            id: (int) $row['id'],
            name: $row['name'],
        );
    }
}
