ErrorHandler
===

**Ta klasa znajduje się w zagnieżdżonej przestrzeni nazw `_Private` — nie należy ręcznie tworzyć jej instancji. Aby uzyskać dostęp do obiektu tej klasy, należy użyć składni `WT()->errors`.**

Mechanizm wychwytujący i obsługujący systemowe błędy PHP oraz niezłapane wyjątki.

Informacje o błędzie zapisywane są w dzienniku błędów i wyświetlane na ekranie. Obejmują kod błędu (jeśli rzucono wyjątek) lub typ błędu (jeśli wystąpił błąd PHP), wiadomość, ścieżkę do pliku i linię pliku oraz backtrace.

## `handleException(\Throwable $exception) : void`

Wychwytuje niezłapany wyjątek. Metoda przeznaczona do zarejestrowania przez `set_exception_handler()`.

Dodaje informacje do dziennika błędów za pomocą metody `addToLog()`. Drukuje komunikat o błędzie w formie strony HTML bądź w formie komunikatu tekstowego (gdy skrypt jest uruchamiany w wierszu polecenia).

## `handleError(int $number, string $message, string $file, int $line) : void`

Konwertuje błąd systemowy PHP na wyjątek za pośrednictwem wbudowanej klasy `ErrorException`. Metoda przeznaczona do zarejestrowania przez `set_error_handler()`.

Uwaga: rzucane jako wyjątek są wszystkie błędy, nawet typu `E_NOTICE`. Nie jest uwzględniana wartość dyrektywy `error_reporting`. Ignorowane są jedynie błędy, przy których wystąpieniu użyto [operatora kontroli błędów `@`](http://php.net/manual/en/language.operators.errorcontrol.php).

## `addToLog(\Throwable $exception) : bool`

Dopisuje informacje o błędzie do dziennika błędów, jeśli określono ścieżkę do pliku dziennika błędów za pomocą `logFilePath()`. Zwraca prawdę, jeśli zapis do dziennika powiódł się, inaczej zwraca fałsz.

## `setShowDetails(bool $setting) : void`

Włącza bądź wyłącza szczegóły w komunikatach błędów w formie HTML. Gdy szczegóły są wyłączone, dla bezpieczeństwa zamiast pełnej informacji o błędzie ukazuje się jedynie komunikat „Przepraszamy za usterki”.

## `getShowDetails() : bool`

Zwraca aktualną wartość ustawienia szczegółów komunikatów błędów.

## `setLogFilePath(?string $logFilePath) : void`

Ustawia ścieżkę do pliku, w którym funkcja `addToLog()` ma zapisywać komunikaty do dziennika błędów na `$logFilePath`. Jeżeli argument jest równy `null`, wyłącza zapis do dziennika błędów, co jest zachowaniem domyślnym.

## `getLogFilePath() : ?string`

Zwraca bieżącą ścieżkę do pliku dziennika błędów lub `null`, jeśli tej ścieżki nie określono.