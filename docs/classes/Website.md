Website
===

Kontroler reprezentujący witrynę internetową w systemie WizyTówka. Dziedziczy po klasie `Controller`.

## *static* `URL($target, array $arguments = [])`

Zwraca pełny adres URL kierujący do określonej w argumencie `$target` strony w witrynie internetowej. Argument `$target` może być liczbą całkowitą (typ `integer`) — wtedy zwrócony zostanie adres strony o określonym identyfikatorze (pole `id`) bądź fałsz, jeśli strona o takim identyfikatorze nie istnieje. Jeśli strona jest stroną główną witryny, slug w adresie URL zostanie pominięty. W innym wypadku argument `$target` uważany jest za slug strony — wtedy też istnienie strony nie jest weryfikowane.

Argument `$arguments` określa parametry dodane do query stringa odnośnika.

Jeżeli funkcja prostych odnośników (ustawienie `websitePrettyLinks`) jest włączona, adres pozbawiony będzie query stringa, chyba że określony będzie argument `$arguments` (na przykład `http://jankowalski.pl/kontakt`). W przeciwnym razie zostanie dodany argument `id` ze slugiem strony (na przykład `http://jankowalski.pl/?id=kontakt`).

W związku z tym nie należy określać w query stringu argumentu o kluczu `id`, gdyż zostanie wtedy rzucony wyjątek `ControllerException` #2.