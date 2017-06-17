File
===

Klasa reprezentująca przesłany plik (rekord w tabeli bazy danych). Dziedziczy po klasie `DatabaseObject`.

Posiada następujące pola:

- `id` — klucz podstawowy,
- `name` — unikalna nazwa pliku,
- `userId` — identyfikator użytkownika, który wysłał plik na serwer; jeśli `NULL` — użytkownik został usunięty,
- `uploadedTime` — data i czas wysłania pliku na serwer w formie uniksowego znacznika czasu.

## *static* `getByName($name)`

Zwraca plik o nazwie `$name` lub fałsz, jeśli brak takiego pliku.
