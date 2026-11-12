<?php

declare(strict_types=1);

namespace MrVaga\Home;

/**
 * A candidate's application. The score is what the employer's shortlist needs,
 * and the stage moves forward only one step at a time.
 */
final class Application
{
    public const STAGES = ['applied', 'screening', 'interview', 'offer', 'hired'];

    public function __construct(
        public readonly string $vacancyId,
        public readonly string $candidate,
        public readonly int $matched,
        public string $stage = 'applied',
    ) {
    }

    public function next(): void
    {
        $index = array_search($this->stage, self::STAGES, true);
        if ($index !== false && $index < count(self::STAGES) - 1) {
            $this->stage = self::STAGES[$index + 1];
        }
    }
}
