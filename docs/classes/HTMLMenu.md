HTMLMenu
===

Klasa generująca menu nawigacyjne HTML oparte na znaczniku `<ul>`. Umożliwia dodawanie i usuwanie odnośników, przypisywanie im klas CSS oraz tworzenie menu zagnieżdżonych.

Poszczególne elementy umieszczane są w znaczniku `<li>`. Wewnątrz niego znajduje się odnośnik `<a>`. Istnieje też alternatywna możliwość podania innej instancji klasy `HTMLMenu` zamiast adresu docelowego, co spowoduje stworzenie menu zagnieżdżonego (`<ul>` wewnątrz elementu).

Klasa implementuje metodę magiczną `__debugInfo()` dla debugowania przy użyciu funkcji `var_dump()`.

Jeśli nie wskazano inaczej, każda metoda zwraca `$this`, co umożliwia tworzenie łańcucha poleceń.

## `__construct($CSSClass = null)`

Konstruktor klasy umożliwia określenie w argumencie `$CSSClass` klasy CSS przypisywanej do znacznika `<ul>`.

## `__toString()`

Klasa umożliwia rzutowanie na typ ciągu znaków, w celu wygenerowania kodu HTML menu i wyświetlenia go lub przekazania.

## `add($label, $content, $CSSClass = null, $position = null, $newTab = false)`

Metoda dodaje element do menu. Argument `$label` określa etykietę elementu. Argument `$CSSClass` umożliwia wskazanie klasy CSS przypisywanej do znacznika `<li>`.

Argument `$content` określa zawartość elementu menu. Może być to adres URL w formie ciągu znaków — wtedy zostanie umieszczony znacznik `<a>`, a `$content` określi wartość argumentu `href`. Można też jako argument `$content` podać inną instancję klasy `HTMLMenu` — wtedy zostanie wygenerowane menu zagnieżdżone.

Argument `$newTab` ma zastosowanie tylko, gdy `$content` jest adresem URL i określa, czy znacznik `<a>` otrzymać ma atrybut `target="_blank"`.

W argumencie `$position` można określić pozycję elementu menu w formie liczbowej. Jeśli argument ten nie zostanie określony, element menu zostanie dodany na jego koniec. Jeśli wystąpi kilka elementów menu z określoną tą samą pozycją, ich ułożenie będzie nieprzewidywalne, zgodnie z [dokumentacją funkcji wbudowanej `sort()`](http://php.net/manual/en/function.sort.php#refsect1-function.sort-description).

## `remove($label)`

Metoda usuwa z menu wszystkie elementy z etykietą określoną jako `$label`.