*abstract* HTMLTag
===

Klasa gromadząca abstrakcję używaną w klasach generujących znaczniki HTML takich jak `HTMLHead`, `HTMLFormFields`, `HTMLMenu`.

Aby wygenerować kod HTML elementu obsługiwanego przez klasę dziedziczącą, należy wywołać metodę `output()` kierującą na wyjście generowany kod HTML bądź rzutować instancję klasy na ciąg znaków.

Niektóre metody klas dziedziczących umożliwiają określenie dodatkowych atrybutów znaczników HTML za pośrednictwem argumentu `$HTMLAttributes` w formie tablicy. Zawartość tej tablicy jest parsowana zgodnie z zasadami określonymi w metodzie chronionej `_renderHTMLOpenTag()`.

Uwaga: treści przekazywane klasom dziedziczącym umieszczane w kodzie HTML, jeśli nie wskazano inaczej, zazwyczaj nie są filtrowane — należy je przefiltrować przez `HTML::escape()`.

## `__construct(string $CSSClass = null)`

Konstruktor umożliwia określenie klasy CSS dla głównego znacznika HTML generowanego przez klasę.

## `__toString() : string`

Klasa umożliwia rzutowanie swoich instancji na ciąg znaków w celu zapisania bądź wyświetlenia kodu HTML. Metoda zwraca tekst kierowany na wyjście przez metodę `output()`.

## `getCSSClass() : string`

Zwraca określoną klasę CSS głównego znacznika HTML.

## `setCSSClass(string $CSSClass) : void`

Ustawia klasę CSS głównego znacznika HTML na `$CSSClass`.

## *abstract* `output() : void`

Generuje kod HTML i kieruje go na wyjście.

Klasa dziedzicząca powinna w tym miejscu zaimplementować wyświetlanie elementu, renderowanie poprzez kod HTML.

## *protected* `_renderHTMLOpenTag(string $tagName, array $HTMLAttributes = []) : void`

Metoda używana wewnętrznie przez klasy dziedziczące przeznaczona do generowania kodu HTML znacznika otwierającego `$tagName` wraz z atrybutami wskazanymi w tablicy `$HTMLAttributes`. Na przykład: `<input type="text" name="field_1" disabled>`. Kod HTML jest kierowany na wyjście, nie — zwracany.

Klucze tablicy `$HTMLAttributes` są nazwami atrybutów, jej wartości są ich wartościami. Jeżeli wartość atrybutu jest typem logicznym o wartości prawda, generowana jest sama nazwa atrybutu bez wartości; jeśli zaś jest typem logicznym o wartości fałsz — atrybut jest pomijany.