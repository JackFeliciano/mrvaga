<?php

declare(strict_types=1);

namespace MrVaga\Home;

/**
 * A vacancy is what an employer offers: a role, a place, a mode of work and a
 * monthly range in whole cents. The range always runs low to high.
 */
final class Vacancy
{
    public function __construct(
        public readonly string $id,
        public readonly string $employer,
        public readonly string $role,
        public readonly string $city,
        public readonly string $mode,
        public readonly int $lowCents,
        public readonly int $highCents,
        public readonly int $openings,
    ) {
    }
}
