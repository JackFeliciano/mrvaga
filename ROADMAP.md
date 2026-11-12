# Roadmap

The project holds a working board today. The model in src/Home/ covers the vacancy, the application, the board and the demo sample with vacancy VG-104. The code in src/App/ serves the page route and the probe route. The showroom directory holds the demo entry file and the stylesheet. The suite in tests/ runs 21 checks. The deploy directory holds the systemd unit and the nginx location block. The list below names improvements a contributor could make next.

1. A contributor could add vacancy search. A candidate could filter the open vacancies by place, contract kind or pay band. This work would touch Board.php, the router and the HTML in src/App/.

2. A contributor could add storage. The board could keep vacancies and applications between runs. This work would touch Board.php and a new class in src/Home/.

3. A contributor could add employer accounts. An employer could log in and see only their own vacancies. This work would touch the router, the front controller and a new class in src/Home/.

4. A contributor could add a rejected stage. The employer could end an application without a hire. This work would touch Application.php and Board.php.

5. A contributor could add vacancy editing. The employer could correct a title or withdraw a vacancy before the closing day. This work would touch Vacancy.php and Board.php.
