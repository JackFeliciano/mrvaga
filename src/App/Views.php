<?php

declare(strict_types=1);

namespace MrVaga\App;

use MrVaga\Home\Board;
use MrVaga\Home\Sample;

final class Views
{
    public function __construct(private readonly string $base)
    {
    }

    private function h(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    private function money(int $cents): string
    {
        return 'EUR ' . number_format(intdiv($cents, 100), 0, '.', ',') . ',' . sprintf('%02d', $cents % 100);
    }

    public function header(string $title): string
    {
        $base = $this->h($this->base);

        return <<<HTML
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{$this->h($title)}</title>
<base href="{$base}">
<link rel="stylesheet" href="assets/site.css">
</head>
<body>
<main>
HTML;
    }

    public function footer(): string
    {
        return "</main>\n</body>\n</html>\n";
    }

    public function notFound(string $path): string
    {
        return $this->header('Not found')
            . '<h1>Not found</h1>' . "\n"
            . '<p class="stage">' . $this->h($path) . ' is not a route here.</p>' . "\n"
            . $this->footer();
    }

    public function front(Sample $sample, string $today): string
    {
        $board = $sample->board();
        $out = $this->header('MrVaga');
        $out .= '<h1>MrVaga</h1>' . "\n";
        $out .= '<p class="lede">Employers post the vacancy. Candidates apply. The shortlist moves one stage at a time and stops when the openings fill.</p>' . "\n";
        $out .= '<p class="stage">Board as of ' . $this->h($today) . '.</p>' . "\n";

        $out .= '<h2>Open vacancies</h2>' . "\n";
        $out .= '<div class="cards">' . "\n";
        foreach ($board->open() as $vacancy) {
            $out .= '<article class="card">' . "\n";
            $out .= '<h3>' . $this->h($vacancy->role) . '</h3>' . "\n";
            $out .= '<p>' . $this->h($vacancy->employer) . ', ' . $this->h($vacancy->city) . ', ' . $this->h($vacancy->mode) . '.</p>' . "\n";
            $out .= '<dl>';
            $out .= '<dt>Range</dt><dd>' . $this->money($vacancy->lowCents) . ' to ' . $this->money($vacancy->highCents) . '</dd>';
            $out .= '<dt>Openings</dt><dd>' . $vacancy->openings . ', ' . $board->fillPercent($vacancy->id) . ' percent filled</dd>';
            $out .= '<dt>Applicants</dt><dd>' . count($board->applicants($vacancy->id)) . '</dd>';
            $out .= '</dl>' . "\n";
            $out .= '</article>' . "\n";
        }
        $out .= '</div>' . "\n";

        $out .= $this->shortlist($board, 'VG-103');
        $out .= $this->closed($board, 'VG-104');
        $out .= $this->footer();

        return $out;
    }

    private function shortlist(Board $board, string $vacancyId): string
    {
        $vacancy = $board->vacancy($vacancyId);
        $out = '<h2>Shortlist for ' . $this->h($vacancy->role) . '</h2>' . "\n";
        $out .= '<table class="tiers">' . "\n";
        $out .= '<thead><tr><th>Candidate</th><th>Match</th><th>Stage</th></tr></thead>' . "\n";
        $out .= '<tbody>' . "\n";
        foreach ($board->applicants($vacancyId) as $application) {
            $out .= '<tr><td>' . $this->h($application->candidate) . '</td>';
            $out .= '<td>' . $application->matched . '</td>';
            $out .= '<td><span class="badge on">' . $this->h($application->stage) . '</span></td></tr>' . "\n";
        }
        $out .= '</tbody>' . "\n" . '</table>' . "\n";

        return $out;
    }

    private function closed(Board $board, string $vacancyId): string
    {
        $vacancy = $board->vacancy($vacancyId);
        $out = '<h2>Filled vacancy</h2>' . "\n";
        $out .= '<div class="cards"><article class="card">' . "\n";
        $out .= '<h3>' . $this->h($vacancy->role) . '</h3>' . "\n";
        $out .= '<p class="stage">' . $this->h($vacancy->employer) . ' has ' . $board->hired($vacancyId) . ' of ' . $vacancy->openings . ' in place.</p>' . "\n";
        $out .= '<dl>';
        $out .= '<dt>Open to applications</dt><dd>' . ($board->isClosed($vacancyId) ? 'no' : 'yes') . '</dd>';
        $out .= '<dt>Filled</dt><dd>' . $board->fillPercent($vacancyId) . ' percent</dd>';
        $out .= '</dl>' . "\n";
        $out .= '</article></div>' . "\n";

        return $out;
    }
}
