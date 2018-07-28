ReadOnlyArray
===

**Ta klasa znajduje się w zagnieżdżonej przestrzeni nazw `_Private` — nie należy ręcznie tworzyć jej instancji. Aby uzyskać dostęp do obiektu tej klasy, należy odczytać zawartość zmiennych globalnych `$_GET`, `$_POST`, `$_FILES`, `$_SERVER`, `$_COOKIE`.**

Obiekty tej klasy imitują tablicę z elementami tylko do odczytu. Implementując interfejs `ArrayAccess`, klasa umożliwia dostęp do zawartości tablicy przy użyciu natywnej składni bez możliwości wprowadzania zmian w jej elementach.

Klasa implementuje interfejsy `Countable` i `IteratorAggregate`, by umożliwiać iterowanie po tablicy w pętli oraz policzenie jej elementów. Implementuje również metodę `__debugInfo()` dla funkcji `var_dump()`.

Jeżeli nastąpi próba bezpośredniej modyfikacji zawartości tablicy, zostanie rzucony wyjątek `ReadOnlyArrayException` #1.

## `__construct(array $data, string $name = null)`

Argument `$data` określa tablicę, której zawartość ma przechowywać nowo tworzony obiekt. Opcjonalny drugi argument `$name` określa nazwę zmiennej podanej w argumencie `$data` — nazwa ta jest dla czytelności używana w komunikacie błędu.

## `overwrite($key, $value) : void`

Nadpisuje element tablicy o kluczu `$key`, standardowo przeznaczony tylko do odczytu, wartością `$value`. Metoda przeznaczona wyłącznie do wewnętrznego użytku systemu i do testów jednostkowych. Używaj jej jedynie, jeśli dokładnie wiesz, co robisz.