HooksManager
===

**Ta klasa znajduje się w zagnieżdżonej przestrzeni nazw `_Private` — nie należy ręcznie tworzyć jej instancji. Aby uzyskać dostęp do obiektu tej klasy, należy użyć składni `WT()->hooks`.**

Menadżer haków — akcji i filtrów — w systemie WizyTówka. Koncepcja zainspirowana akcjami i filtrami systemu [WordPress](https://codex.wordpress.org/Plugin_API).

Akcje i filtry są wywołaniami zwrotnymi (callbackami) zaczepianymi zdalnie do określonego miejsca kodu identyfikowanego nazwą. Akcje mogą wykonywać dowolne czynności i kierować tekst na wyjście. Filtry zaś powinny jedynie operować na otrzymanych danych i zwracać je w zmodyfikowanej formie.

Filtry i akcje mogą otrzymywać argumenty przy uruchamianiu. Filtry muszą otrzymywać co najmniej jeden argument. Jeśli liczba wymaganych przez callback argumentów jest większa niż liczba argumentów otrzymanych przy uruchamianiu filtra lub akcji, rzucany jest wyjątek `HooksManagerException` #1.

## `addAction(string $name, callable $callback) : void`

Dodaje akcję do zbioru akcji zaczepionych do miejsca określonego nazwą w argumencie `$name`. Argument `$callback` przeznaczony jest na określenie wywołania zwrotnego — funkcję anonimową bądź nazwę funkcji lub metody. Więcej informacji o określaniu callbacków na stronie [www.php.net](http://php.net/manual/en/language.types.callable.php).

## `addFilter(string $name, callable $callback) : void`

Działa identycznie jak `addAction()`, lecz dodaje filtr.

## `removeAction(string $name, callable $callback) : void`

Usuwa akcję ze zbioru akcji zaczepionych do miejsca określonego nazwą w argumencie `$name`. W argumencie `$callback` należy określić usuwaną funkcję bądź metodę.

Jeżeli zbiór `$name` nie istnieje, jest rzucany wyjątek `HooksManagerException` #2.

## `removeFilter(string $name, callable $callback) : void`

Działa jak `removeAction()`, lecz usuwa filtr.

## `runAction(string $name, $arg1, $arg2, …) : void`

Uruchamia wszystkie akcje zaczepione do miejsca zaczepienia określonego nazwą `$name`. Do wszystkich callbacków zostaną przekazane argumenty określone w argumentach `$arg1`, `$arg2` i kolejnych. Nie ma ograniczeń co do liczby przekazanych argumentów, nie są też one wymagane.

Jeżeli któryś z callbacków ma liczbę wymaganych argumentów większą niż liczba przekazanych argumentów, zostanie rzucony wyjątek `HooksManagerException` #1.

## `applyFilter(string $name, $arg1, $arg2, …)`

Działa podobnie jak `runAction()` — uruchamia wszystkie filtry przypisane do miejsca zaczepienia `$name`. Jest jednak ważna różnica. Jako że filtry z założenia mają modyfikować wartość, wymagane jest podane co najmniej jednego argumentu przekazywanego filtrom. Jego wartość zmodyfikowana przez filtry jest zwracana.

Jeżeli nie zostanie podany choć jeden argument, zostanie rzucony wyjątek `HooksManagerException` #3.