ConfigurationFile
===

Klasa gromadząca różne konfiguracje przechowywane w plikach JSON.

Implementuje metody magiczne `__get()`, `__set()`, `__isset()`, `__unset()`, umożliwiając operowanie na poszczególnych ustawieniach jak na polach obiektu. Implementuje też interfejsy `Countable` i `IteratorAggregate`, by umożliwiać iterowanie po poszczególnych ustawieniach w pętli oraz ich policzenie. Implementuje również metodę `__debugInfo()` dla funkcji `var_dump()`.

Odczyt pliku następuje w konstruktorze. Zapis pliku następuje w destruktorze wyłącznie, gdy jakiekolwiek ustawienie zostanie zmodyfikowane bądź usunięte.

Plik JSON powinien zawierać tablicę wartości, inaczej przy odczycie zostanie rzucony wyjątek #4.

## `__construct($filename, $readOnly = false)`

Jako `$filename` przyjmuje ścieżkę do pliku konfiguracyjnego w formacie JSON. Argument `$readOnly` powinien być typu `boolean` i określa, czy konfiguracja jest tylko do odczytu. Wtedy, jeśli nastąpi próba zmiany wartości ustawienia, zostanie rzucony wyjątek #19.

Jeżeli plik JSON nie istnieje, wystąpi błąd. Jeżeli dojdzie do błędu podczas parsowania pliku JSON, zostanie rzucony wyjątek #2.

## *static* `createNew($filename)`

Tworzy nowy pusty plik konfiguracyjny w formacie JSON. Jako `$filename` przyjmuje ścieżkę do pliku. Jeżeli plik istnieje, zostanie nadpisany.