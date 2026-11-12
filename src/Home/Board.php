<?php

declare(strict_types=1);

namespace MrVaga\Home;

use InvalidArgumentException;

/**
 * The board. Vacancies go up, candidates apply, the employer moves a shortlist
 * along. A vacancy closes when every opening is filled.
 */
final class Board
{
    /** @var array<string, Vacancy> */
    private array $vacancies = [];

    /** @var array<string, list<Application>> */
    private array $applications = [];

    public function post(Vacancy $vacancy): void
    {
        if ($vacancy->lowCents > $vacancy->highCents) {
            throw new InvalidArgumentException('The low end of a range cannot sit above the high end.');
        }
        if ($vacancy->openings <= 0) {
            throw new InvalidArgumentException('A vacancy needs at least one opening.');
        }
        $this->vacancies[$vacancy->id] = $vacancy;
        $this->applications[$vacancy->id] = [];
    }

    public function vacancy(string $id): Vacancy
    {
        if (!isset($this->vacancies[$id])) {
            throw new InvalidArgumentException('No vacancy with id ' . $id . '.');
        }

        return $this->vacancies[$id];
    }

    /**
     * One candidate applies once to one vacancy. A repeated application returns
     * false and leaves the first in place.
     */
    public function apply(Application $application): bool
    {
        $this->vacancy($application->vacancyId);
        foreach ($this->applications[$application->vacancyId] as $existing) {
            if ($existing->candidate === $application->candidate) {
                return false;
            }
        }
        if ($this->hired($application->vacancyId) >= $this->vacancy($application->vacancyId)->openings) {
            return false;
        }
        $this->applications[$application->vacancyId][] = $application;

        return true;
    }

    /** @return list<Application> */
    public function applicants(string $vacancyId): array
    {
        $list = $this->applications[$vacancyId] ?? [];
        usort($list, static function (Application $a, Application $b): int {
            return [$b->matched, $a->candidate] <=> [$a->matched, $b->candidate];
        });

        return $list;
    }

    public function advance(string $vacancyId, string $candidate): bool
    {
        foreach ($this->applications[$vacancyId] ?? [] as $application) {
            if ($application->candidate === $candidate) {
                $application->next();

                return true;
            }
        }

        return false;
    }

    public function stage(string $vacancyId, string $candidate): ?string
    {
        foreach ($this->applications[$vacancyId] ?? [] as $application) {
            if ($application->candidate === $candidate) {
                return $application->stage;
            }
        }

        return null;
    }

    public function hired(string $vacancyId): int
    {
        $count = 0;
        foreach ($this->applications[$vacancyId] ?? [] as $application) {
            if ($application->stage === 'hired') {
                $count++;
            }
        }

        return $count;
    }

    public function isClosed(string $vacancyId): bool
    {
        return $this->hired($vacancyId) >= $this->vacancy($vacancyId)->openings;
    }

    /** @return list<Vacancy> */
    public function open(): array
    {
        $out = [];
        foreach ($this->vacancies as $id => $vacancy) {
            if (!$this->isClosed($id)) {
                $out[] = $vacancy;
            }
        }

        return $out;
    }

    /**
     * How far through its hiring a vacancy is, as a whole percentage. A vacancy
     * with three openings and one hire reads 33.
     */
    public function fillPercent(string $vacancyId): int
    {
        $openings = $this->vacancy($vacancyId)->openings;
        if ($openings <= 0) {
            return 0;
        }

        return intdiv($this->hired($vacancyId) * 100, $openings);
    }
}
