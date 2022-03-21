### Uruchamianie projektu
Utwórz plik `.env.local` na podstawie pliku `.env` oraz plik `./docker/.env`
na podstawie pliku `./docker/.env.dist`. Zbuduj kontenery dockerowe poleceniem
`docker-compose up -d --build`.

Wewnątrz kontenera core-php (możesz do niego wejść poleceniem `docke exec -it
core-php bash`), korzystając z PHP8 i Composera, zainstaluj zależności
komendą `composer install`.

By zapełnić bazę testowymi danymi, wykonaj wewnątrz kontenera polecenie
`bin/console app:fixtures:load`.

Po skonfigurowaniu aplikacji w ten sposób będziesz mieć do niej dostęp pod
adresem 127.0.0.1, na porcie 8000.

### Zadanie
Zapoznaj się z design patternem Factory, a następnie zaproponuj implementację
klasy `App\Service\KeyGenerator\KeyGeneratorFactory`. Utwórz kontroler z akcją
umożliwiającą wygenerowanie klucza dla danej platformy i gry.
