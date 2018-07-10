HTMLMessage
===

Niewielka klasa renderująca kod HTML komunikatów (umieszczanych zazwyczaj na górze strony) o powodzeniu operacji lub napotkanym błędzie. Klasa dziedziczy po klasie `HTMLTag`.

Kod generowany przez klasę ma następującą formę:

	<div class="message success" role="alert">Treść wiadomości.</div>

Ostatnia klasa CSS zależna jest od typu komunikatu, pierwsza zaś wskazywana jest w konstruktorze (z klasy `HTMLTag`). Jeżeli nie określono żadnego komunikatu, nie jest generowany żaden kod HTML.

Klasa implementuje metodę magiczną `__debugInfo()` w celu debugowania przy użyciu funkcji `var_dump()`.

## `__construct(string $CSSClass = null, ?string $messageBoxName = null)`

Jeżeli argument `$messageBoxName` określa unikalną nazwę, niewyświetlony w bieżącym żądaniu komunikat zostanie zachowany i przywrócony przy kolejnym żądaniu HTTP. Funkcja ta jest użyteczna, jeśli po wykonanej operacji ma zostać wykonane przekierowanie pod inny adres, gdzie powinien ukazać się określony przez przekierowaniem komunikat. Funkcja działa jedynie, jeśli użytkownik jest zalogowany w menadżerze sesji.

Zobacz `HTMLTag::__construct()`.

## `success(string $message, $arg1, $arg2, …) : void`

Określa komunikat o powodzeniu (klasa CSS: `success`) o treści `$message`. Jeżeli określono wcześniej inny komunikat, zostaje on nadpisany.

Opcjonalnie można przekazać dodatkowe argumenty `$arg1`, `$arg2` i kolejne. W przypadku, gdy zostaną podane, zostaną wyescapowane za pośrednictwem `HTML::escape()`, a wynikowy komunikat zostanie spreparowany za pomocą funkcji wbudowanej `sprintf()`, do której argumenty zostaną przekazane razem z treścią komunikatu `$message` — musi być ona sformatowana zgodnie z [dokumentacją funkcji `sprintf()`](http://php.net/manual/en/function.sprintf.php), inaczej wystąpi błąd.

## `error(string $message, $arg1, $arg2, …) : void`

Określa komunikat o błędzie (klasa CSS: `error`) o treści `$message`. Zobacz opis metody `success()`.

## `info(string $message, $arg1, $arg2, …) : void`

Określa neutralny komunikat (klasa CSS: `info`) o treści `$message`. Zobacz opis metody `success()`.

## `information(...)`

Alias dla metody `info()`.

## `default(string $message, $arg1, $arg2, …) : void`

Działa jak metoda `success()` z tą różnicą, że nie nadpisuje określonego wcześniej komunikatu. Określony przez tę metodę komunikat o powodzeniu zostanie wyrenderowany wyłącznie, jeśli nie określono żadnego innego komunikatu.

## `clear(bool $default = false) : void`

Czyści komunikat błędu, jeśli został określony.

Należy określić argument `$default` o wartości prawda, jeśli oczekiwane jest także wyczyszczenie komunikatu ustawionego przez metodę `default()`.

## `output() : void`

Renderuje kod HTML komunikatu.