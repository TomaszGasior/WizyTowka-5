ErrorHandler
===

Mechanizm wychwytujący i obsługujący systemowe błędy PHP oraz niezłapane wyjątki.

Informacja o błędzie obejmuje: kod błędu (jeśli jest wyjątkiem) lub typ błędu (jeśli jest przekonwertowanym błędem PHP), wiadomość, ścieżkę do pliku i linię pliku oraz ścieżkę wykonywanych plików (backtrace).

## *static* `handleException(\Throwable $exception)`

Wychwytuje niezłapany wyjątek. Metoda przeznaczona do zarejestrowania przez `set_exception_handler()`.

Dodaje informacje do dziennika błędów za pomocą metody `addToLog()`. Drukuje komunikat o błędzie, używając metody `_printAsPlainText()` (gdy zostanie wykryty typ MIME inny niż `text/html` lub gdy skrypt jest uruchamiany w wierszu polecenia) bądź `_printAsHTML()`.

## *static* `handleError($number, $message, $file, $line)`

Konwertuje błąd systemowy PHP na wyjątek za pośrednictwem wbudowanej klasy `ErrorException`. Metoda przeznaczona do zarejestrowania przez `set_error_handler()`.

Uwaga: rzucane jako wyjątek są wszystkie błędy, nawet typu `E_NOTICE`. Nie jest uwzględniana wartość dyrektywy `error_reporting`. Ignorowane są jedynie błędy, przy których wystąpieniu użyto [operatora kontroli błędów `@`](http://php.net/manual/en/language.operators.errorcontrol.php).

## *static* `addToLog(\Throwable $exception)`

Dopisuje informacje o błędzie do dziennika błędów. Znajduje się on w pliku `errors.log`, domyślnie w folderze `data/config`.