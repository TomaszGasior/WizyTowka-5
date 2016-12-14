Database
===

Menadżer połączenia z bazą danych. Umożliwia zainicjowanie połączenia z obsługiwanym systemem bazodanowym za pośrednictwem PDO oraz pobranie obiektu klasy `PDO` w celu wykonywania operacji na bazie.

## *static* `connect($driver, $database, $host = null, $login = null, $password = null)`

Rozpoczyna połączenie z bazą danych, tworząc instancję klasy `PDO`. Argument `$driver` określa typ bazy danych, obsługiwane są: `mysql`, `pgsql`, `sqlite`. Jeśli typ bazy danych będzie nieprawidłowy, zostanie rzucony wyjątek #8.

Dla baz danych MySQL i PostgreSQL argumenty `$database`, `$host`, `$login` i `$password` przeznaczone są kolejno na nazwę bazy danych, adres serwera bazodanowego, nazwę użytkownika i hasło.
W przypadku SQLite argument `$database` powinien zawierać ścieżkę do pliku bazy danych, a pozostałe argumenty są nieużywane.

Jeśli połączenie zostało już wcześniej rozpoczęte, zostanie rzucony wyjątek #7.

## *static* `disconnect()`

Kończy bieżące połączenie z bazą danych, umożliwiając rozpoczęcie kolejnego.

## *static* `pdo()`

Zwraca instancję klasy `PDO` stworzoną podczas inicjowania połączenia z bazą danych. Jeśli połączenie nie zostało jeszcze rozpoczęte, zostanie rzucony wyjątek #9.

## *static* `executeSQL($sql)`

Umożliwia wykonanie dowolnego zapytania SQL za pośrednictwem metody `PDO::exec()`. Zwraca wartość zwracaną przez tę metodę (liczbę zmodyfikowanych rekordów lub fałsz).