<?php

declare(strict_types=1);

namespace MrVaga\Home;

final class Sample
{
    public static function board(): Board
    {
        $board = new Board();
        $board->post(new Vacancy('VG-101', 'Padaria Sao Jorge', 'Baker', 'Lisbon', 'onsite', 140000, 175000, 2));
        $board->post(new Vacancy('VG-102', 'Nordeste Solar', 'Field technician', 'Fortaleza', 'onsite', 320000, 410000, 1));
        $board->post(new Vacancy('VG-103', 'Linha Azul', 'Logistics analyst', 'Porto', 'hybrid', 210000, 260000, 3));
        $board->post(new Vacancy('VG-104', 'Casa Verde', 'Nursery assistant', 'Braga', 'onsite', 115000, 130000, 1));

        $board->apply(new Application('VG-101', 'Ana Ferreira', 82));
        $board->apply(new Application('VG-101', 'Bruno Matos', 74));
        $board->apply(new Application('VG-103', 'Carla Nunes', 91));
        $board->apply(new Application('VG-103', 'Diogo Reis', 68));
        $board->apply(new Application('VG-104', 'Elsa Pires', 88));

        $board->advance('VG-103', 'Carla Nunes');
        $board->advance('VG-103', 'Carla Nunes');
        $board->advance('VG-104', 'Elsa Pires');
        $board->advance('VG-104', 'Elsa Pires');
        $board->advance('VG-104', 'Elsa Pires');
        $board->advance('VG-104', 'Elsa Pires');

        return $board;
    }
}
