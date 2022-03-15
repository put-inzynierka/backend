### Uruchamianie projektu
Korzystając z PHP8 i Composera, zainstaluj zależności komendą
`composer install`. Skopiuj plik .env do pliku .env.local i uzupełnij plik
.env.local o dane swojej bazy danych (pod kluczem DATABASE_URL).

Po skonfigurowaniu aplikacji w ten sposób możesz uruchomić ją poleceniem
`php -S 127.0.0.1:8000 -t public`. Aplikacja będzie wtedy dostępna pod adresem
127.0.0.1, na porcie 8000.

### Zadanie
Zapoznaj się z design patternem Factory, a następnie zaproponuj implementację
klasy `App\Service\KeyGenerator\KeyGeneratorFactory`. Utwórz kontroler z akcją
umożliwiającą wygenerowanie klucza dla danej platformy i gry.