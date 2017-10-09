PageBox
===

Klasa reprezentująca blok treści w podstronie witryny (rekord w tabeli bazy danych). Dziedziczy po klasie `DatabaseObject`.

Posiada następujące pola:

- `id` — klucz podstawowy;
- `type` — nazwa typu zawartości;
- `contents` — treść typu zawartości (obiekt zakodowany w formacie JSON);
- `settings` — ustawienia typu zawartości (obiekt zakodowany w formacie JSON);
- `pageId` — identyfikator podstrony witryny, do której przynależy blok treści;
- `positionRow` — numer wiersza, w którym blok treści ma zostać wyświetlony;
- `positionColumn` — numer kolumny w ramach wiersza, w której blok treści ma zostać wyświetlony.

## *static* `getAll($pageId)`

Zwraca tablicę bloków treści podstrony witryny o identyfikatorze `$pageId` bądź pustą tablicę, jeśli żadne bloki treści nie są przypisane do podstrony o podanym identyfikatorze, a więc jeśli podstrona jest pusta.

**Uwaga: ta metoda nadpisuje metodę o tej samej nazwie z klasy `DatabaseObject`. W przypadku klasy `PageBox` możliwość pobrania wszystkich istniejących bloków treści nie jest użyteczna, dlatego metoda `PageBox::getAll()` wymaga podania identyfikatora podstrony.**