<?php

declare(strict_types=1);

namespace App\Encounters;

final readonly class Encounter
{
    public function __construct(
        public int $id,
        public string $location,
        public string $description,
        public int $speciesId,
    ) {}

    /**
     * @param array<string, mixed> $row
     */
    public static function fromDatabaseRow(array $row): self
    {
        return new self(
            id: (int) $row['id'],
            location: $row['location'],
            description: $row['description'],
            speciesId: (int) $row['species_id'],
        );
    }
}
