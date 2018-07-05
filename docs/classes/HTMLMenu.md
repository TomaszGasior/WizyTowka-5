HTMLMenu
===

Klasa generująca menu nawigacyjne HTML oparte na znaczniku `<ul>`. Umożliwia dodawanie i usuwanie odnośników, przypisywanie im klas CSS oraz tworzenie menu zagnieżdżonych. Klasa dziedziczy po klasie `HTMLTag`.

Poszczególne elementy umieszczane są w znaczniku `<li>`. Zazwyczaj wewnątrz niego znajduje się odnośnik `<a>`. Istnieje też alternatywna możliwość podania innej instancji klasy `HTMLMenu` zamiast adresu docelowego, co spowoduje stworzenie menu zagnieżdżonego (`<ul>` wewnątrz elementu).

Klasa implementuje interfejsy `Countable` i `IteratorAggregate`, by umożliwiać iterowanie w pętli oraz policzenie elementów menu, a także metodę magiczną `__debugInfo()` dla debugowania przy użyciu funkcji `var_dump()`.

Jeśli nie wskazano inaczej, każda metoda zwraca `$this`, co umożliwia tworzenie łańcucha poleceń.

## `append(string $label, $content, ?string $CSSClass = null, array $HTMLAttributes = [], bool $visible = true) : HTMLMenu`

Dodaje element na koniec menu.

Argument `$label` określa etykietę elementu. Uwaga: etykieta nie jest escapowana — niepożądane znaczniki HTML muszą zostać usunięte ręcznie.

Argument `$content` określa zawartość elementu menu. Może być to adres URL w formie ciągu znaków — wtedy zostanie umieszczony znacznik `<a>`, a `$content` określi wartość atrybutu `href`. Można też jako argument `$content` podać inną instancję klasy `HTMLMenu` — wtedy zostanie wygenerowane menu zagnieżdżone. Zostanie rzucony wyjątek `HTMLMenuException` #2, jeśli wartość argumentu będzie niepoprawna.

Opcjonalny argument `$CSSClass` umożliwia wskazanie klasy CSS przypisywanej do znacznika `<li>`. Opcjonalny argument `$HTMLAttributes` umożliwia określenie dodatkowych atrybutów znacznika `<a>`. Należy podać go jako tablicę — jej klucze zostaną nazwami atrybutów, a wartości ich wartościami. Argument ma zastosowanie tylko, gdy `$content` jest adresem URL.

Opcjonalny argument `$visible` o wartości logicznej będącej fałszem ukrywa element menu (nie będzie on w ogóle renderowany w kodzie HTML). Dzięki jego zastosowaniu można uniknąć przesunięcia numeracji pozostałych elementów menu.

## `prepend(...) : HTMLMenu`

Dodaje element na początek menu. Przyjmuje argumenty jak `append()`.

## `insert($position, ...) : HTMLMenu`

Dodaje element menu na pozycję określoną argumentem `$position`. Pozostałe argumenty przyjmuje jak `append()`.

W argumencie `$position` należy określić pozycję elementu menu w formie liczby dodatniej bądź ujemnej. Argument `$position` musi być liczbą całkowitą lub zmiennoprzecinkową. W innym wypadku zostanie rzucony wyjątek `HTMLMenuException` #3.

Jeśli wystąpi kilka elementów menu z określoną tą samą pozycją, zostaną posortowane według etykiety.

Uwaga: pozycje elementów menu dodanych za pomocą metod `append()` i `prepend()` są automatyczne. Może dojść do sytuacji, że elementy dodane za pośrednictwem `prepend()` otrzymają pozycje o wartości ujemnej.

## `replace($position, ...) : HTMLMenu`

Usuwa z menu wszystkie elementy z pozycją `$position`, a następnie dodaje element menu w tej pozycji. Pozostałe argumenty przyjmuje jak `append()`.

Alias dla wywołania metod `removeByPosition($position)` i `insert($position, ...)`.

## `removeByPosition($position) : HTMLMenu`

Usuwa z menu wszystkie elementy z pozycją określoną jako `$position`.

Argument `$position` musi być liczbą całkowitą lub zmiennoprzecinkową. W innym wypadku zostanie rzucony wyjątek `HTMLMenuException` #3.

## `removeByContent($content) : HTMLMenu`

Usuwa z menu wszystkie elementy z inną instancją klasy bądź adresem URL `$content`.

## `removeByLabel(string $label) : HTMLMenu`

Usuwa z menu wszystkie elementy z etykietą `$label`.

## `output() : void`

Jeśli menu nie jest puste, generuje kod HTML menu — listę `<ul>` z elementami `<li>`.

Zostanie rzucony wyjątek `HTMLMenuException` #1, jeśli jako element menu do instancji klasy została dodana ona sama.

Metoda nie zwraca wartości.