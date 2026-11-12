<?php

declare(strict_types=1);

namespace MrVaga\Tests;

use MrVaga\Home\Application;
use MrVaga\Home\Board;
use MrVaga\Home\Sample;
use MrVaga\Home\Vacancy;

return [
    ['a range may not run backwards', static function (): void {
        $board = new Board();
        try {
            $board->post(new Vacancy('V-1', 'Shop', 'Baker', 'Lisbon', 'onsite', 200000, 100000, 1));
            Assert::true(false, 'an inverted range was accepted');
        } catch (\InvalidArgumentException) {
            Assert::true(true);
        }
    }],
    ['a vacancy needs an opening', static function (): void {
        $board = new Board();
        try {
            $board->post(new Vacancy('V-1', 'Shop', 'Baker', 'Lisbon', 'onsite', 100000, 200000, 0));
            Assert::true(false, 'zero openings were accepted');
        } catch (\InvalidArgumentException) {
            Assert::true(true);
        }
    }],
    ['a candidate applies once', static function (): void {
        $board = new Board();
        $board->post(new Vacancy('V-1', 'Shop', 'Baker', 'Lisbon', 'onsite', 100000, 200000, 2));
        Assert::true($board->apply(new Application('V-1', 'Ana', 70)));
        Assert::false($board->apply(new Application('V-1', 'Ana', 90)));
        Assert::count(1, $board->applicants('V-1'));
        Assert::same(70, $board->applicants('V-1')[0]->matched);
    }],
    ['applicants sort by match then name', static function (): void {
        $board = new Board();
        $board->post(new Vacancy('V-1', 'Shop', 'Baker', 'Lisbon', 'onsite', 100000, 200000, 3));
        $board->apply(new Application('V-1', 'Bruno', 80));
        $board->apply(new Application('V-1', 'Ana', 80));
        $board->apply(new Application('V-1', 'Carla', 95));
        $names = array_map(static fn (Application $a): string => $a->candidate, $board->applicants('V-1'));
        Assert::same(['Carla', 'Ana', 'Bruno'], $names);
    }],
    ['a stage steps forward and stops at hired', static function (): void {
        $board = new Board();
        $board->post(new Vacancy('V-1', 'Shop', 'Baker', 'Lisbon', 'onsite', 100000, 200000, 1));
        $board->apply(new Application('V-1', 'Ana', 90));
        Assert::same('applied', $board->stage('V-1', 'Ana'));
        $board->advance('V-1', 'Ana');
        Assert::same('screening', $board->stage('V-1', 'Ana'));
        for ($i = 0; $i < 10; $i++) {
            $board->advance('V-1', 'Ana');
        }
        Assert::same('hired', $board->stage('V-1', 'Ana'));
    }],
    ['advancing an unknown candidate fails', static function (): void {
        $board = new Board();
        $board->post(new Vacancy('V-1', 'Shop', 'Baker', 'Lisbon', 'onsite', 100000, 200000, 1));
        Assert::false($board->advance('V-1', 'Nobody'));
        Assert::same(null, $board->stage('V-1', 'Nobody'));
    }],
    ['filled openings close the vacancy', static function (): void {
        $board = new Board();
        $board->post(new Vacancy('V-1', 'Shop', 'Baker', 'Lisbon', 'onsite', 100000, 200000, 1));
        $board->apply(new Application('V-1', 'Ana', 90));
        for ($i = 0; $i < 5; $i++) {
            $board->advance('V-1', 'Ana');
        }
        Assert::true($board->isClosed('V-1'));
        Assert::false($board->apply(new Application('V-1', 'Bruno', 80)));
        Assert::count(1, $board->applicants('V-1'));
    }],
    ['the fill percentage counts whole openings', static function (): void {
        $board = new Board();
        $board->post(new Vacancy('V-1', 'Shop', 'Baker', 'Lisbon', 'onsite', 100000, 200000, 3));
        $board->apply(new Application('V-1', 'Ana', 90));
        for ($i = 0; $i < 5; $i++) {
            $board->advance('V-1', 'Ana');
        }
        Assert::same(33, $board->fillPercent('V-1'));
    }],
    ['the sample board has three open vacancies', static function (): void {
        $board = Sample::board();
        Assert::count(3, $board->open());
        Assert::true($board->isClosed('VG-104'));
        Assert::same(100, $board->fillPercent('VG-104'));
    }],
];
