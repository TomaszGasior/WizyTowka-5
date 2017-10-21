PageBox
===

Klasa reprezentująca blok treści (rekord w tabeli bazy danych) będący częścią strony w witrynie. Dziedziczy po klasie `DatabaseObject`.

Posiada następujące pola:

- `id` — klucz podstawowy;
- `type` — nazwa typu zawartości;
- `contents` — treść typu zawartości (obiekt zakodowany w formacie JSON);
- `settings` — ustawienia typu zawartości (obiekt zakodowany w formacie JSON);
- `pageId` — identyfikator strony witryny, do której przynależy blok treści;
- `positionRow` — numer wiersza, w którym blok treści ma zostać wyświetlony;
- `positionColumn` — numer kolumny w ramach wiersza, w której blok treści ma zostać wyświetlony.

## *static* `getAll($pageId = null)`

Zwraca tablicę bloków treści strony witryny o identyfikatorze `$pageId` bądź pustą tablicę, jeśli `$pageId` jest puste lub żadne bloki treści nie są przypisane do strony o podanym identyfikatorze, a więc jeśli strona jest pusta.

**Uwaga: ta metoda nadpisuje metodę o tej samej nazwie z klasy `DatabaseObject`. W przypadku klasy `PageBox` możliwość pobrania wszystkich istniejących bloków treści nie jest użyteczna, dlatego metoda `PageBox::getAll()` wymaga podania identyfikatora strony.**