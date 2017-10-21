ContentType
===

Klasa reprezentująca typy zawartości stron (np. galeria zdjęć, miniblog, formularz kontaktowy). Dziedziczy po klasie `Addon`.

Dodatki tego typu znajdują się w folderze `addons/types` w katalogu danych witryny oraz w katalogu systemu.

W pliku konfiguracyjnym `addon.conf` wtyczka powinna określać:

- `namespace` — przestrzeń nazw gromadząca wszystkie klasy typu zawartości, będą one automatycznie ładowane z podfolderu `classes`;
- `label` — etykieta typu zawartości używana w interfejsie graficznym;
- `contents` — tablica określająca domyślną zawartość obiektu `$contents` przechowującego treść strony typu zawartości w witrynie, opcjonalne;
- `settings` — tablica określająca domyślną zawartość obiektu `$settings` przechowującego ustawienia strony typu zawartości w witrynie, opcjonalne.

Typ zawartości musi składać się z trzech klas dziedziczących po klasie `ContentTypeAPI`:

- `WebsitePageBox` — klasa obsługująca renderowanie i logikę strony typu zawartości w witrynie;
- `EditorPage` — klasa obsługująca stronę panelu administracyjnego służącą do edycji treści strony w witrynie;
- `SettingsPage` — klasa obsługująca stronę panelu administracyjnego służącą do zmiany ustawień strony w witrynie.

## `initWebsitePageBox()`

Inicjuje i zwraca instancję klasy `WebsitePageBox`. Metoda używana wewnętrznie przez system.

Jeżeli klasa nie dziedziczy po klasie `ContentTypeAPI`, zostanie rzucony wyjątek `ContentTypeException` #1.

## `initEditorPage()`

Inicjuje i zwraca instancję klasy `EditorPage`. Metoda używana wewnętrznie przez system.

Jeżeli klasa nie dziedziczy po klasie `ContentTypeAPI`, zostanie rzucony wyjątek `ContentTypeException` #1.

## `initSettingsPage()`

Inicjuje i zwraca instancję klasy `SettingsPage`. Metoda używana wewnętrznie przez system.

Jeżeli klasa nie dziedziczy po klasie `ContentTypeAPI`, zostanie rzucony wyjątek `ContentTypeException` #1.