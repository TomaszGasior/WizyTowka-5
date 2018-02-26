Page
===

Klasa reprezentująca stronę w witrynie (rekord w tabeli bazy danych). Dziedziczy po klasie `DatabaseObject`.

Posiada następujące pola:

- `id` — klucz podstawowy;
- `slug` — unikalna nazwa uproszczona strony;
- `title` — tytuł strony;
- `titleHead` — tytuł w nagłówku `<head>` witryny;
- `description` — opis w znaczniku `<meta name="description">`;
- `noIndex` — indeksowanie w wyszukiwarkach: jeśli `0` — indeksować, jeśli `1` — prosić o nieindeksowanie;
- `isDraft` — status strony zapisany w formie liczby całkowitej: jeśli `0` — publiczna, jeśli `1` — szkic (strona niedostępna publicznie);
- `contentType` — nazwa typu zawartości (dokładna nazwa dodatku);
- `contents` — treść typu zawartości (obiekt zakodowany w formacie JSON);
- `settings` — ustawienia typu zawartości (obiekt zakodowany w formacie JSON);
- `userId` — identyfikator użytkownika, który stworzył stronę; jeśli `null` — użytkownik został usunięty;
- `updatedTime` — data i czas ostatniej aktualizacji strony w formie uniksowego znacznika czasu;
- `createdTime` — data i czas utworzenia strony w formie uniksowego znacznika czasu.

## *static* `getAll()`

Zwraca tablicę stron publicznych (dostępnych publicznie, niebędących szkicami). Jeśli brak takich stron, zwracana jest pusta tablica.

**Uwaga: ta metoda nadpisuje metodę o tej samej nazwie z klasy `DatabaseObject`. Intencja jest następująca: `getAll()` w przypadku klasy `Pages` zwraca tablicę stron publicznie dostępnych, zaś `getAllDrafts()` zwraca tablicę stron będących szkicami.**

## *static* `getAllDrafts()`

Zwraca tablicę stron o statusie szkicu (niedostępnych publicznie). Jeśli brak takich stron, zwracana jest pusta tablica.

## *static* `getBySlug($slug)`

Zwraca stronę z nazwą uproszczoną (slugiem) równą podanej w argumencie `$slug` lub fałsz, jeśli brak takiej strony.
