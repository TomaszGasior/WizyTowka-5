ErrorHandler
===

**Ta klasa znajduje się w zagnieżdżonej przestrzeni nazw `_Private` — nie należy ręcznie tworzyć jej instancji. Aby uzyskać dostęp do obiektu tej klasy, należy użyć składni `WT()->errors`.**

Mechanizm wychwytujący i obsługujący systemowe błędy PHP oraz niezłapane wyjątki.

Informacje o błędzie zapisywane są w dzienniku błędów i wyświetlane na ekranie. Obejmują kod błędu (jeśli rzucono wyjątek) lub typ błędu (jeśli wystąpił błąd PHP), wiadomość, ścieżkę do pliku i linię pliku oraz backtrace.

## `__construct($logFilePath)`

W argumencie `$logFilePath` należy określić ścieżkę do pliku, w którym funkcja `addToLog()` ma zapisywać komunikaty do dziennika błędów.

## `handleException(\Throwable $exception)`

Wychwytuje niezłapany wyjątek. Metoda przeznaczona do zarejestrowania przez `set_exception_handler()`.

Dodaje informacje do dziennika błędów za pomocą metody `addToLog()`. Drukuje komunikat o błędzie w formie strony HTML bądź w formie komunikatu tekstowego (gdy skrypt jest uruchamiany w wierszu polecenia).

## `handleError($number, $message, $file, $line)`

Konwertuje błąd systemowy PHP na wyjątek za pośrednictwem wbudowanej klasy `ErrorException`. Metoda przeznaczona do zarejestrowania przez `set_error_handler()`.

Uwaga: rzucane jako wyjątek są wszystkie błędy, nawet typu `E_NOTICE`. Nie jest uwzględniana wartość dyrektywy `error_reporting`. Ignorowane są jedynie błędy, przy których wystąpieniu użyto [operatora kontroli błędów `@`](http://php.net/manual/en/language.operators.errorcontrol.php).

## `addToLog(\Throwable $exception)`

Dopisuje informacje o błędzie do dziennika błędów, do pliku określonego w konstruktorze.

## `showErrorDetails($setting = null)`

Jeśli określono argument `$setting` o wartości logicznej, włącza bądź wyłącza szczegóły w komunikatach błędów HTML. Gdy szczegóły są wyłączone, dla bezpieczeństwa zamiast pełnej informacji o błędzie ukazuje się jedynie komunikat „Przepraszamy za usterki”.

Jeśli argumentu nie określono, zwraca bieżące ustawienie.