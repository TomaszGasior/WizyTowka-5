Page
===

Klasa reprezentująca podstronę witryny (rekord w tabeli bazy danych). Dziedziczy po klasie `DatabaseObject`.

Posiada następujące pola:

- `id` — klucz podstawowy,
- `slug` — unikalna nazwa uproszczona podstrony,
- `contentType` — nazwa typu zawartości,
- `title` — tytuł podstrony,
- `titleHead` — tytuł w nagłówku `<head>` witryny,
- `description` — opis w znaczniku meta `description`,
- `isDraft` — status podstrony zapisany w formie liczby całkowitej: jeśli `0` — publiczna, jeśli `1` — szkic,
- `contents` — treść witryny (obiekt zakodowany w formacie JSON),
- `settings` — ustawienia typu zawartości (obiekt zakodowany w formacie JSON),
- `userId` — identyfikator użytkownika, który stworzył podstronę,
- `languageId` — identyfikator języka podstrony,
- `updatedTime` — data i czas ostatniej aktualizacji podstrony w formie uniksowego znacznika czasu,
- `createdTime` — data i czas utworzenia podstrony w formie uniksowego znacznika czasu.

## *static* `getAll()`

Zwraca tablicę podstron publicznych (dostępnych publicznie, niebędących szkicami). Jeśli brak takich podstron, zwracana jest pusta tablica.

**Uwaga: ta metoda nadpisuje metodę o tej samej nazwie z klasy `DatabaseObject`. Intencja jest następująca: `getAll()` w przypadku klasy `Pages` zwraca tablicę stron publicznie dostępnych, zaś `getAllDrafts()` zwraca tablicę stron będących szkicami.**

## *static* `getAllDrafts()`

Zwraca tablicę podstron o statusie szkicu (niedostępnych publicznie). Jeśli brak takich podstron, zwracana jest pusta tablica.

## *static* `getByLanguageId($languageId)`

Zwraca tablicę podstron (niebędących szkicami, dostępnych publicznie) z identyfikatorem języka równym `$languageId`. Jeśli brak takich podstron, zwracana jest pusta tablica.

## *static* `getDraftsByLanguageId($languageId)`

Działa tak samo jak `getByLanguageId()`, ale zwraca podstrony o statusie szkicu.

## *static* `getBySlug($slug)`

Zwraca podstronę z nazwą uproszczoną (slugiem) równą podanej w argumencie `$slug` lub fałsz, jeśli brak takiej podstrony.