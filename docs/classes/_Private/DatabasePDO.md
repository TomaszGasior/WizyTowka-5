DatabasePDO
===

**Ta klasa znajduje się w zagnieżdżonej przestrzeni nazw `_Private` — nie należy ręcznie tworzyć jej instancji. Aby uzyskać dostęp do obiektu tej klasy, należy użyć składni `WT()->database`.**

Obwoluta obiektu `\PDO` z konstruktorem dopasowanym do potrzeb systemu WizyTówka. Klasa dziedziczy po klasie `\PDO`.

## `__construct($driver, $database, $host = null, $login = null, $password = null)`

Rozpoczyna połączenie z bazą danych. Argument `$driver` określa typ bazy danych, obsługiwane są: `mysql`, `pgsql`, `sqlite`. Jeśli typ bazy danych będzie nieprawidłowy, zostanie rzucony wyjątek `DatabasePDOException` #1.

Dla baz danych MySQL i PostgreSQL argumenty `$database`, `$host`, `$login` i `$password` przeznaczone są kolejno na nazwę bazy danych, adres serwera bazodanowego, nazwę użytkownika i hasło.
W przypadku SQLite argument `$database` powinien zawierać ścieżkę do pliku bazy danych, a pozostałe argumenty są nieużywane.