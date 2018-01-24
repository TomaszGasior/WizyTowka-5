Text
===

Klasa przechowująca ciąg znaków (string) i zdolna do wykonywania na nim operacji. Wszystkie metody tej klasy opierają swoje działanie na multibajtowych funkcjach PHP służących do operowania na stringach.

Klasa implementuje metodę `__debugInfo()` dla funkcji `var_dump()`, a także metodę `__toString()`, umożliwiając przekształcenie obiektu tej klasy na przechowywany ciąg znaków. Implementując interfejsy `ArrayAccess` i `IteratorAggregate`, klasa umożliwia odczyt i modyfikację pojedynczych znaków ciągu poprzez traktowanie instancji klasy jak tablicy, a także iterowanie po instancji klasy w pętli w celu odczytu poszczególnych znaków.

Każda metoda, chyba że wskazano inaczej, zwraca instancję klasy (`$this` z wnętrza klasy), dzięki czemu można tworzyć konstrukcję łańcuchową metod.

## `__construct($string)`

Konstruktor jako argument przyjmuje przechowywany ciąg znaków. Jeśli zawartość zmiennej `$string` nie jest typu `string`, następuje konwersja do tego typu.

## `get()`

Zwraca przechowywany ciąg znaków.

Zamiast tej metody można rzutować obiekt na typ `string` lub użyć względem obiektu polecenia `echo`.

## `getChar($position)`

Zwraca jeden znak na pozycji określonej argumentem `$position`. Pozycje są numerowane od zera. Jeśli zostanie podana liczba ujemna, znak będzie liczony od końca. Jeżeli znak nie istnieje, zostanie zwrócone `null`.

Argument `$position` musi być liczbą całkowitą.

## `getLength()`

Zwraca długość przechowywanego ciągu znaków.

## `lowercase()`

Zmienia wielkość liter przechowywanego tekstu na małe.

## `uppercase()`

Zmienia wielkość liter przechowywanego tekstu na duże.

## `cut($from, $length)`

Ucina przechowywany ciąg znaków. Argument `$from` określa numer znaku rozpoczynającego wynikowy ciąg znaków. Znaki numerowane są od zera. Jeśli liczba jest ujemna, znak jest określany od końca. Argument `$length` określa długość wynikowego ciągu znaków. Jeśli jest dodatni, wynikowy ciąg będzie miał tyle znaków. Jeśli jest ujemny, o tyle znaków będzie krótszy.

Argumenty `$from` i `$length` muszą być liczbami całkowitymi.

## `replace(array $replacements, $caseInsensitive = false)`

Zamienia w przechowywanym ciągu znaków teksty określone w tablicy `$replacements`: poszczególne klucze tablicy są zamieniane na odpowiadające im wartości. Jeżeli argument `$caseInsensitive` jest prawdą, nie jest uwzględniana przy zamianie wielkość znaków.

## `correctTypography($flags)`

Modyfikuje przechowywany ciąg znaków, dokonując poprawek typograficznych, aby poprawić zgodność z polskimi zasadami typografii. Zakres aplikowanych poprawek należy określić, podając flagi w argumencie `$flags` w formie stałych klasy `Text`:

* `TYPOGRAPHY_QUOTES` — cudzysłowy `""` są zamienianie na poprawne polskie `„”` (brak obsługi cytatów zagnieżdżonych);
* `TYPOGRAPHY_ORPHANS` — wyrazy jednoznakowe ([spójniki i przyimki](https://pl.wikipedia.org/wiki/Sierotka_(typografia)#Przykłady)) są przenoszone z końca wiersza do następnego wiersza z wykorzystaniem znaku niełamliwej spacji;
* `TYPOGRAPHY_DASHES` — minusy otoczone spacjami zamieniane są na długi myślnik `—`;
* `TYPOGRAPHY_OTHER` — dokonywane są korekty znaków apostrofu i wielokropka.

Metoda jest przystosowana do pracy z kodem HTML. Korekty nie są dokonywane w znacznikach otwierających HTML oraz we wnętrzu znaczników `<pre>` i `<code>`. Nie są używane encje HTML, zamiast nich odpowiednie znaki są wstawiane bezpośrednio.

## `makeFragment($maxLength, $dots = '…')`

Zamienia przechowywany ciąg znaków na wycinek zawierający fragment oryginalnego ciągu znaków o długości nieprzekraczającej `$maxLength` oraz doklejoną po nim zawartość argumentu `$dots`.

Jeżeli podczas odcinania fragmentu ostatnie słowo zostanie uszkodzone, zostanie usunięte.

Argument `$maxLength` musi być dodatnią liczbą całkowitą.

## `makeMiddleFragment($maxLength, $dots = ' … ')`

Zamienia przechowywany ciąg znaków na wycinek zawierający początkowy fragment oryginalnego ciągu znaków o długości nieprzekraczającej połowy `$maxLength`, końcowy fragment oryginalnego ciągu znaków o długości nieprzekraczającej połowy `$maxLength` oraz doklejoną pomiędzy tymi fragmentami zawartość argumentu `$dots`.

Jeżeli podczas odcinania fragmentu graniczne słowo zostanie uszkodzone, zostanie usunięte.

Argument `$maxLength` musi być dodatnią liczbą całkowitą.

## `makeSlug($lowercase = true)`

Zamienia przechowywany ciąg znaków na identyfikator, który może zostać bezpiecznie użyty jako nazwa pliku w systemie plików bądź fragment adresu URL. Jeżeli argument `$lowercase` jest prawdą, znaki zamieniane są na małe.

Spacje są zamieniane na minusy, ich duplikaty są usuwane. Polskie znaki diakrytyczne są zamieniane na ich odpowiedniki z tablicy ASCII. Wszystkie niepożądane znaki są usuwane.

## `formatAsDateTime($format = '%Y-%m-%d %H:%M:%S')`

Zamienia przechowywany ciąg znaków na datę i godzinę w formacie określonym w argumencie `$format`.

Ciąg znaków jest najpierw konwertowany do uniksowego znacznika czasu za pomocą funkcji `strtotime()` (chyba, że string zawiera tylko liczby — wtedy jest używany bezpośrednio). Następnie oryginalny string jest zastępowany przez datę i godzinę zwróconą przez funkcję `strftime()`.

Format określony w argumencie `$format` musi być zatem zgodny ze [składnią formatu funkcji `strftime()`](http://php.net/manual/en/function.strftime.php#refsect1-function.strftime-parameters).

## `formatAsFileSize($binaryUnits = true)`

Zamienia przechowywany ciąg znaków na tekst określający rozmiar pliku w czytelnym dla użytkownika formacie.

Jeżeli ciąg znaków zawiera wyłącznie cyfry, jest traktowany jak rozmiar pliku w bajtach. Po zamianie ciąg znaków zawiera przekonwertowaną liczbę określającą rozmiar pliku wraz z jednostką (np. megabajty); elementy połączone są niełamliwą spacją.

Jeżeli argument `$binaryUnits` jest prawdą, używane są jednostki zgodne z układem SI (np. kilobajt = 1000 bajtów), w przeciwnym wypadku używane są jednostki binarne (np. kibibajt = 1024 bajty).