#ConfigurationFile

Klasa gromadząca różne konfiguracje przechowywane w plikach JSON. 

Implementuje metody magiczne `__get`, `__set`, `__isset`, `__unset`, umożliwiając operowanie na poszczególnych ustawieniach tak, jak na polach obiektu. 
Implementuje też interfejs `IteratorAggregate`, by umożliwiać iterowanie po poszczególnych ustawieniach w pętli. 
Implementuje również metodę `__debugInfo` dla funkcji `var_dump` (dostępne od PHP 5.6).

Odczyt pliku następuje w konstruktorze. Zapis pliku następuje w destruktorze wyłącznie, gdy jakiekolwiek ustawienie zostanie zmodyfikowane bądź usunięte.

##`__construct($filename)`

Jako `$filename` przyjmuje ścieżkę do pliku konfiguracyjnego w formacie JSON.

##*static* `createNew($filename)`

Tworzy nowy pusty plik konfiguracyjny w formacie JSON. Jako `$filename` przyjmuje ścieżkę do pliku.