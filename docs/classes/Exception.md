*abstract* Exception
===

Własna klasa wyjątków systemu WizyTówka. Wszystkie rzucane przez system WizyTówka wyjątki są instancjami tej klasy. Klasa dziedziczy po klasie wbudowanej o tej samej nazwie.

W systemie WizyTówka została określona koncepcja rzucania wyjątków z użyciem metod statycznych klasy wyjątku. Obok każdej klasy rzucającej wyjątki (w tym samym pliku) znajduje się klasa o tej samej nazwie z przyrostkiem `Exception` (na przykład `DatabaseException` dla klasy `Database`) dziedzicząca po tej klasie `Exception`. Klasa wyjątku definiuje publiczne metody statyczne zwracające swoją instancję z określonym komunikatem błędu i kodem.

Zalety takiego rozwiązania są następujące:

- treść komunikatów błędu jest oddzielona od miejsca wystąpienia błędu, co wpływa na czytelność;
- jeśli sytuacja, w której występuje wyjątek, powtarza się, nie trzeba kopiować kodu wyjątku;
- wszystkie wyjątki danej klasy są zgrupowane w jednym miejscu (na końcu pliku klasy).

Koncepcja została zaczerpnięta od [Rossa Tucka](http://rosstuck.com/formatting-exception-messages/).

Konstruktor klasy `Exception` wymaga określenia komunikatu błędu i kodu. Wystąpi błąd, jeśli nastąpi próba utworzenia instancji klasy wyjątku bezpośrednio, a nie przez publiczną metodę statyczną. Błąd wystąpi również, jeśli klasa rzuci wyjątek nienależący do niej (np. klasa `Page` rzuci wyjątek `DatabaseException` zamiast `PageException`), chyba że klasa dziedziczy po klasie, do której należy wyjątek.