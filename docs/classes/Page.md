Page
===

Klasa reprezentująca podstronę witryny (rekord w tabeli bazy danych). Dziedziczy po klasie `DatabaseObject`.

Posiada następujące pola:

- `id` — klucz podstawowy,
- `slug` — unikalna nazwa uproszczona podstrony,
- `title` — tytuł podstrony,
- `titleHead` — tytuł w nagłówku `<head>` witryny,
- `description` — opis w znaczniku meta `description`,
- `keywords` — słowa kluczowe w znaczniku meta `keywords`,
- `isDraft` — status podstrony zapisany w formie liczby całkowitej: jeśli `0` — publiczna, jeśli `1` — szkic,
- `userId` — identyfikator użytkownika, który stworzył podstronę; jeśli `NULL` — użytkownik został usunięty,
- `updatedTime` — data i czas ostatniej aktualizacji podstrony w formie uniksowego znacznika czasu,
- `createdTime` — data i czas utworzenia podstrony w formie uniksowego znacznika czasu.

## *static* `getAll()`

Zwraca tablicę podstron publicznych (dostępnych publicznie, niebędących szkicami). Jeśli brak takich podstron, zwracana jest pusta tablica.

**Uwaga: ta metoda nadpisuje metodę o tej samej nazwie z klasy `DatabaseObject`. Intencja jest następująca: `getAll()` w przypadku klasy `Pages` zwraca tablicę stron publicznie dostępnych, zaś `getAllDrafts()` zwraca tablicę stron będących szkicami.**

## *static* `getAllDrafts()`

Zwraca tablicę podstron o statusie szkicu (niedostępnych publicznie). Jeśli brak takich podstron, zwracana jest pusta tablica.

## *static* `getBySlug($slug)`

Zwraca podstronę z nazwą uproszczoną (slugiem) równą podanej w argumencie `$slug` lub fałsz, jeśli brak takiej podstrony.
