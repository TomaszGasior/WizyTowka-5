HTMLMessage
===

Niewielka klasa renderująca kod HTML komunikatów (umieszczanych zazwyczaj na górze strony) o powodzeniu operacji lub napotkanym błędzie. Klasa dziedziczy po klasie `HTMLTag`.

Kod generowany przez klasę ma następującą formę:

	<div class="message success" role="alert">Treść wiadomości.</div>

Ostatnia klasa CSS zależna jest od typu komunikatu, pierwsza zaś wskazywana jest w konstruktorze (z klasy `HTMLTag`). Jeżeli nie określono żadnego komunikatu, nie jest generowany żaden kod HTML.

Klasa implementuje metodę magiczną `__debugInfo()` w celu debugowania przy użyciu funkcji `var_dump()`.

## `success($message)`

Określa komunikat o powodzeniu (klasa CSS: `success`) o treści `$message`. Jeżeli określono wcześniej inny komunikat, zostaje on nadpisany.

## `error($message)`

Określa komunikat o błędzie (klasa CSS: `error`) o treści `$message`. Jeżeli określono wcześniej inny komunikat, zostaje on nadpisany.

## `info($message)`

Określa neutralny komunikat (klasa CSS: `info`) o treści `$message`. Jeżeli określono wcześniej inny komunikat, zostaje on nadpisany.

## `information(...)`

Alias dla metody `info()`.

## `default($message)`

Działa jak metoda `success()` z tą różnicą, że nie nadpisuje określonego wcześniej komunikatu. Określony przez tę metodę komunikat o powodzeniu zostanie wyrenderowany wyłącznie, jeśli nie określono żadnego innego komunikatu.

## `clear($default = false)`

Czyści komunikat błędu, jeśli został określony.

Należy określić argument `$default` o wartości prawda, jeśli oczekiwane jest także wyczyszczenie komunikatu ustawionego przez metodę `default()`.

## `output()`

Renderuje kod HTML komunikatu.