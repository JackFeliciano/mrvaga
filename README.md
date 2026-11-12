# MrVaga

## What the project is

MrVaga is a job board. An employer posts a vacancy on the board. A candidate applies to that vacancy once. The board refuses a second application from the same candidate.

Every vacancy carries its salary along with the information the board requires. A vacancy that leaves any of them out never reaches the board. The employer reads the applications in the order they arrived and moves one candidate along.

The Vacancy class holds the vacancy and answers whether it takes an application on a given day. The Application class holds one application and its stage. The stages are applied, screening, interview, offer and hired. The Board class posts a vacancy, takes an application and refuses a second one from the same candidate. The Board class also refuses any application after the closing day, lists the applicants in arrival order and advances a candidate one stage.

## How to run it

The command below starts the demo server from the demo entry file.

```
php -S 127.0.0.1:8113 -t showroom showroom/index.php
```

The router reads the base path from the MRVAGA_BASE environment variable and strips that base from the request path. The nginx location block serves the site under /mrvaga/. The variable takes this value:

```sh
MRVAGA_BASE=/mrvaga/
```
The command below runs the suite through the hand-written runner.

```
php tests/runner.php
```

## What the routes answer

The page route / answers the HTML page. The page shows the demo board that Sample.php builds with vacancy VG-104.

The probe route /health answers JSON. The JSON carries the keys ok, vacancies and hired.

## How the layout is arranged

autoload.php loads the classes.

- src/Home/ holds the model. Vacancy.php holds a vacancy, Application.php holds one application, Board.php holds the board and Sample.php builds the demo board.
- src/App/ holds the router, the front controller and the HTML.
- showroom/ holds the demo entry file and the stylesheet.
- tests/ holds the hand-written runner, the assertion helper and the test file.
- deploy/ holds the systemd unit and the nginx location block.

## The state of the suite

The suite runs 21 checks. The command below runs them.

```
php tests/runner.php
```

## Licence

The repository uses the Mozilla Public License 2.0. The licence lets anyone use, copy, change and share the code. A contributor who shares a changed file must keep that file under the same licence. Anyone may combine these files with other code under other terms.
