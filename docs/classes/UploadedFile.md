UploadedFile
===

Klasa reprezentująca plik przesłany jako załącznik bądź medium do strony.

Wysłane pliki domyślnie przechowywane są w folderze `data/files`. Klasa umożliwia pobranie ich listy oraz usuwanie i zmienianie nazw.

## *private* `__construct()`

Konstruktor jest prywatny — nie można tworzyć nowych plików w ten sposób. Aby dodać nowo przesłany plik, należy ręcznie umieścić go w folderze wysłanych plików (domyślnie `data/files`) z użyciem funkcji operujących na systemie plików. Aby operować na już istniejącym pliku, należy skorzystać z metody `getByName()`.

## *private* `__clone()`

Nie można klonować obiektu pliku. Aby skopiować plik, należy dokonać tego ręcznie z użyciem funkcji operujących na systemie plików.

## `getName()`

Zwraca nazwę pliku.

## `getPath()`

Zwraca pełną bezwzględną ścieżkę do pliku w systemie plików.

## `getURL()`

Zwraca pełen adres URL pliku.

## `getSize()`

Zwraca rozmiar pliku w bajtach formie liczby całkowitej. Jeśli plik jest pusty, bądź nie istnieje, zwraca liczbę zero.

## `getModificationTime()`

Zwraca czas ostatniej modyfikacji pliku jako uniksowy znacznik czasu w formie liczby całkowitej. Jeśli plik nie istnieje, zwraca liczbę zero.

## `rename($newFileName)`

Zmienia nazwę pliku na nazwę określoną w argumencie `$newFileName`. Plik o nazwie `$newFileName` nie może istnieć. Argument `$newFileName` nie może zawierać ukośnika bądź ukośnika wstecznego.

Jeżeli zmiana nazwy się powiedzie, zwraca prawdę, inaczej — zwraca fałsz.

## `delete()`

Usuwa plik.

Jeżeli operacja się powiedzie, zwraca prawdę, inaczej — zwraca fałsz.

## *static* `getByName($fileName)`

Zwraca wysłany plik (instancję klasy) o nazwie `$fileName` bądź fałsz, jeśli plik o takiej nazwie nie istnieje w folderze przesłanych plików. Nazwa `$fileName` nie może zawierać ukośnika bądź ukośnika wstecznego.

Uwaga: nie są uwzględniane podfoldery folderu wysłanych plików bądź znajdujące się w nich pliki.

## *static* `getAll()`

Zwraca tablicę zawierającą wszystkie przesłane pliki (instancje klasy). Jeśli nie przesłano jeszcze żadnych plików, zwracana jest pusta tablica. Tablica sortowana jest alfabetycznie według nazw plików.

Uwaga: nie są uwzględniane podfoldery folderu wysłanych plików bądź znajdujące się w nich pliki.