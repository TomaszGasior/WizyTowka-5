ConfigurationFile
===

Klasa gromadząca różne konfiguracje przechowywane w plikach JSON.

Implementuje metody magiczne `__get()`, `__set()`, `__isset()`, `__unset()`, umożliwiając operowanie na poszczególnych ustawieniach jak na polach obiektu. Implementuje też interfejsy `Countable` i `IteratorAggregate`, by umożliwiać iterowanie po poszczególnych ustawieniach w pętli oraz ich policzenie. Implementuje również metodę `__debugInfo()` dla funkcji `var_dump()`.

Jeśli zostanie utworzona więcej niż jedna instancja klasy operującej na tym samym pliku, każda z instancji będzie mieć dostęp do najnowszej zawartości pliku konfiguracyjnego — wprowadzane zmiany są synchronizowane. Odczyt z systemu plików zostanie wykonany tylko przy tworzeniu pierwszej instancji.

## `__construct(string $fileName, bool $readOnly = false)`

Jako `$fileName` przyjmuje ścieżkę do pliku konfiguracyjnego w formacie JSON.

Argument `$readOnly` powinien być typu logicznego i określa, czy konfiguracja jest tylko do odczytu. Wtedy, jeśli nastąpi próba zmiany wartości ustawienia, zostanie rzucony wyjątek `ConfigurationFileException` #3.

W konstruktorze następuje odczyt pliku JSON. Jeżeli wskazany plik nie istnieje, wystąpi błąd. Jeżeli dojdzie do błędu podczas parsowania pliku JSON, zostanie rzucony wyjątek `ConfigurationFileException` #1.

Plik JSON powinien zawierać tablicę wartości, inaczej przy odczycie zostanie rzucony wyjątek `ConfigurationFileException` #2.

## `__destruct()`

W destruktorze następuje automatyczny zapis pliku konfiguracyjnego JSON — wywołanie metody `save()`.

## `save() : void`

Zapisuje zawartość pliku konfiguracyjnego do pliku JSON wyłącznie, gdy jakiekolwiek ustawienie zostanie zmodyfikowane bądź usunięte.

## `refresh() : void`

Odświeża zawartość pliku konfiguracyjnego, odczytując jego zawartość na nowo z dysku.

Jeżeli plik nie istnieje, wystąpi błąd. Jeżeli dojdzie do błędu podczas parsowania pliku JSON, zostanie rzucony wyjątek `ConfigurationFileException` #1. Jeżeli kilka instancji klasy `ConfigurationFile` otwarło ten sam plik, odświeżenie nastąpi we wszystkich jednocześnie.

## *static* `createNew($fileName) : void`

Tworzy nowy pusty plik konfiguracyjny w formacie JSON. Jako `$fileName` przyjmuje ścieżkę do pliku. Jeżeli plik istnieje, zostanie nadpisany.