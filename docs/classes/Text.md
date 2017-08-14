Text
===

Klasa przechowująca ciąg znaków (string) i zdolna do wykonywania na nim operacji. Wszystkie metody tej klasy opierają swoje działanie na multibajtowych funkcjach PHP służących do operowania na stringach.

Klasa implementuje metodę `__debugInfo()` dla funkcji `var_dump()`, a także metodę `__toString()`, umożliwiając przekształcenie obiektu tej klasy na przechowywany ciąg znaków.

Każda metoda, chyba że wskazano inaczej, zwraca instancję klasy (`$this` z wnętrza klasy), dzięki czemu można tworzyć konstrukcję łańcuchową metod.

## `__construct($string)`

Konstruktor jako argument przyjmuje przechowywany ciąg znaków. Jeśli zawartość zmiennej `$string` nie jest typu `string`, następuje konwersja do tego typu.

## `get()`

Zwraca przechowywany ciąg znaków.

Zamiast tej metody można rzutować obiekt na typ `string` lub użyć względem obiektu polecenia `echo`.

## `getChar($position)`

Zwraca jeden znak na pozycji określonej argumentem `$position`. Pozycje są numerowane od zera.

Argument `$position` musi być dodatnią liczbą całkowitą.

## `getLength()`

Zwraca długość przechowywanego ciągu znaków.

## `lowercase()`

Zmienia wielkość liter przechowywanego tekstu na małe.

## `uppercase()`

Zmienia wielkość liter przechowywanego tekstu na duże.

## `cut($length)`

Ucina przechowywany ciąg znaków do określonej w `$length` długości. Jeśli liczba jest ujemna, ucina od końca.

## `correctTypography()`

Modyfikuje przechowywany ciąg znaków, dokonując poprawek typograficznych. Aby poprawić zgodność z polskimi zasadami typografii, metoda dokonuje korekty cudzysłowów, apostrofów, myślników i wielokropków. Ponadto spacje za słowami w formie pojedynczych liter (spójniki „i”, „a”; przyimki „o”, „u”, „z”, „w”) zamieniane są na znak niełamliwej spacji.

Metoda jest przystosowana do pracy z kodem HTML. Encje HTML nie są używane, zamiast nich odpowiednie znaki są wstawiane bezpośrednio. Korekty nie są dokonywane w znacznikach otwierających HTML oraz we wnętrzu znaczników `<pre>` i `<code>`.

## `makeFragment($maxLength, $dots = '…')`

Zamienia przechowywany ciąg znaków na wycinek zawierający fragment oryginalnego ciągu znaków o długości nieprzekraczającej `$maxLength` oraz doklejoną po nim zawartość argumentu `$dots`.

Jeżeli podczas odcinania fragmentu ostatnie słowo zostanie uszkodzone, zostanie usunięte.

Argument `$maxLength` musi być dodatnią liczbą całkowitą.

## `makeMiddleFragment($maxLength, $dots = ' … ')`

Zamienia przechowywany ciąg znaków na wycinek zawierający początkowy fragment oryginalnego ciągu znaków o długości nieprzekraczającej połowy `$maxLength`, końcowy fragment oryginalnego ciągu znaków o długości nieprzekraczającej połowy `$maxLength` oraz doklejoną pomiędzy tymi fragmentami zawartość argumentu `$dots`.

Jeżeli podczas odcinania fragmentu graniczne słowo zostanie uszkodzone, zostanie usunięte.

Argument `$maxLength` musi być dodatnią liczbą całkowitą.

## `makeSlug()`

Zamienia przechowywany ciąg znaków na identyfikator, który może zostać bezpiecznie użyty jako nazwa pliku w systemie operacyjnym bądź fragment adresu URL.

Spacje są zamieniane na minusy, ich duplikaty są usuwane. Polskie znaki diakrytyczne są zamieniane na ich odpowiedniki z tablicy ASCII. Wszystkie niepożądane znaki są usuwane.

## `formatAsDateTime($dateFormat = '%Y-%m-%d', $timeFormat = '%H:%M:%S', $reverse = false)`

Zamienia przechowywany ciąg znaków na datę i godzinę w formacie określonym w argumentach `$dateFormat` i `$timeFormat`.

Domyślnie najpierw umieszczana jest data, następnie godzina. Aby pominąć datę bądź godzinę w wynikowym ciągu znaków, należy zamiast formatu podać pusty ciąg bądź `null`. Aby zamienić kolejność, należy ustawić atrybut `$reverse` na prawdę.

Ciąg znaków jest najpierw konwertowany do uniksowego znacznika czasu za pomocą funkcji `strtotime()` (chyba, że string zawiera tylko liczby — wtedy jest używany bezpośrednio). Następnie oryginalny string jest zastępowany przez datę i godzinę zwróconą przez funkcję `strftime()`.

Format określony w argumentach `$dateFormat` i `$timeFormat` musi być zatem zgodny ze [składnią formatu funkcji `strftime()`](http://php.net/manual/en/function.strftime.php#refsect1-function.strftime-parameters).