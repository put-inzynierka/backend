### Uruchamianie projektu
Utwórz plik `.env.local` na podstawie pliku `.env`. Skonfiguruj w nim połączenie z bazą danych oraz dane potrzebne do wysyłki maili.

Korzystając z PHP8.2 i Composera, zainstaluj zależności komendą `composer install`.

Inicjalizuj bazę danych przy pomocy poleceń `bin/console doctrine:database:create` oraz `bin/console doctrine:schema:update --forcez`. By zapełnić bazę testowymi danymi, wykonaj polecenie
`bin/console app:fixtures:load`.

Uruchom serwer developerski poleceniem `php -S 127.0.0.1:8000 -t public`.

Po skonfigurowaniu aplikacji w ten sposób będziesz mieć do niej dostęp pod
adresem 127.0.0.1, na porcie 8000.
