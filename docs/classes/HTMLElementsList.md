HTMLElementsList
===

Generator kodu HTML list elementów (np. listy utworzonych stron, listy przesłanych plików, listy zainstalowanych typów zawartości bądź motywów), zaprojektowany głównie z myślą o panelu administracyjnym systemu. Umożliwia dodanie do poszczególnych pozycji menu nawigacyjnego oraz odnośnika bądź pola wyboru radio. Klasa dziedziczy po klasie `HTMLTag`.

Lista umieszczana jest w znaczniku `<ul>`, a poszczególne jej elementy w znacznikach `<li>`. Należy określić kolekcję danych (tablicę elementów), po której generator kodu HTML będzie iterował w celu wygenerowania listy. Właściwości poszczególnych elementów listy (tytuł, odnośnik, pole radio, menu nawigacyjne) określane są za pomocą callbacków, które jako argument otrzymują pojedynczy element tablicy kolekcji danych. Należy również określić komunikat generowany, gdy kolekcja danych jest pusta.

Jeśli nie wskazano inaczej, każda metoda zwraca `$this`, umożliwiając stworzenie łańcucha poleceń.

## `collection(array &$collection)`

Służy do określenia kolekcji danych. Kolekcja danych test tablicą zawierającą poszczególne elementy, po których generator będzie iterował. Callbacki określone przy użyciu opisanych niżej metod będą otrzymywać każdy z elementów kolekcji danych jako argument.

Określenie kolekcji danych jest obowiązkowe. Argument przyjmowany jest jako referencja.

## `title(callable $callback)`

Umożliwia określenie w argumencie `$callback` callbacka, który jako argument otrzyma element kolekcji danych, a zwrócić ma tytuł danego elementu listy.

Określenie callbacka tytułów elementów jest obowiązkowe.

## `link(callable $callback)`

Dodaje do tytułów elementów odnośnik. W argumencie `$callback` należy wskazać callback, który jako argument otrzyma element kolekcji danych, a zwróci adres URL odnośnika.

Nie można dla tytułu elementu określić jednocześnie pola radio i odnośnika. W takim wypadku zostanie rzucony wyjątek `HTMLElementsListException` #2.

## `radio($name, callable $fieldValueCallback, $currentValue)`

Dodaje do tytułów elementów pole wyboru radio.

Argument `$name` określa atrybut `name` znacznika `<input>`. W argumencie `$fieldValueCallback` należy wskazać callback, który jako argument otrzyma element kolekcji danych, a zwróci wartość pola radio (atrybut `value` znacznika `<input>`). Argument `$currentValue` określa bieżącą wartość przełącznika — jeżeli wartość pola (wartość zwrócona przez wywołanie zwrotne `$fieldValueCallback`) będzie równa `$currentValue`, pole zostanie zaznaczone.

Nie można dla tytułu elementu określić jednocześnie pola radio i odnośnika. W takim wypadku zostanie rzucony wyjątek `HTMLElementsListException` #2.

## `option(...)`

Alias dla metody `radio()`.

## `menu(callable $callback)`

Dodaje do elementów listy menu nawigacyjne. Argument `$callback` służy do określenia callbacka, który jako argument otrzyma element kolekcji danych, a zwróci tablicę z elementami menu.

Menu generowane jest w formie listy `<ul>`.

Tablica elementów menu zawierać powinna tablice zagnieżdżone określające właściwości poszczególnych pozycji menu nawigacyjnego, podane w następującej kolejności:

* etykieta pozycji menu, wymagane,
* adres URL odnośnika menu, wymagane,
* klasa CSS dla elementu menu (znacznika `<li>`), opcjonalnie.

## `emptyMessage($text)`

Służy do określenia w argumencie `$text` komunikatu generowanego zamiast listy elementów, jeśli kolekcja danych jest pusta.

Określenie tego komunikatu jest obowiązkowe.

## `output()`

Generuje kod HTML listy elementów. Jeśli kolekcja nie jest pusta, generowana jest lista `<ul>`. Jeżeli jednak kolekcja danych jest pusta, generowany jest akapit `<p>` z dodatkową klasą CSS `emptyMessage` i określonym komunikatem.

Zostanie rzucony wyjątek `HTMLElementsListException` #1, jeśli nie zostanie określony callback tytułów bądź kolekcja danych, bądź komunikat o pustej kolekcji.

Metoda nie zwraca wartości.