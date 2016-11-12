Page
===

Klasa reprezentująca podstronę witryny (rekord w tabeli bazy danych). Dziedziczy po klasie `DatabaseObject`.

Posiada następujące pola:

- `id` — klucz podstawowy,
- `slug` — unikalna nazwa uproszczona podstrony,
- `contentType` — nazwa typu zawartości,
- `title` — tytuł podstrony,
- `titleMenu` — tytuł w menu nawigacyjnym,
- `titleHead` — tytuł w nagłówku `head` witryny,
- `description` — opis w znaczniku meta `description`,
- `contents` — treść witryny (obiekt zakodowany w formacie JSON),
- `settings` — ustawienia typu zawartości (obiekt zakodowany w formacie JSON),
- `userId` — identyfikator użytkownika, który stworzył podstronę,
- `languageId` — identyfikator języka podstrony,
- `updatedTime` — data i czas ostatniej aktualizacji podstrony w formie uniksowego znacznika czasu,
- `createdTime` — data i czas utworzenia podstrony w formie uniksowego znacznika czasu.

## *static* `getBySlug($slug)`

Zwraca podstronę z nazwą uproszczoną (slugiem) równą podanej w argumencie `$slug` lub fałsz, jeśli brak takiej podstrony.

## *static* `getByLanguageId($languageId)`

Zwraca tablicę podstron z identyfikatorem języka równym `$languageId`. Jeśli brak takich podstron, zwracana jest pusta tablica.