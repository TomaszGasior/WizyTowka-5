User
===

Klasa reprezentująca użytkownika panelu administracyjnego (rekord w tabeli bazy danych). Dziedziczy po klasie `DatabaseObject`.

Posiada następujące pola:

- `id` — klucz podstawowy,
- `name` — unikalna nazwa użytkownika,
- `password` — zahaszowane hasło użytkownika,
- `createdTime` — data i czas utworzenia użytkownika w formie uniksowego znacznika czasu.

W klasie zdefiniowane zostały stałe służące do określania poziomu uprawnień użytkownika.

- `PERM_CREATING_PAGES` — uprawnienie do tworzenia stron i szkiców stron w witrynie,
- `PERM_SENDING_FILES` — uprawnienie do wysyłania plików na serwer,
- `PERM_EDITING_OTHERS_PAGES` — uprawnienie do edycji stron stworzonych przez innych użytkowników,
- `PERM_EDITING_SITE_ELEMENTS` — uprawnienie do modyfikacji elementów witryny (nagłówek, stopka, menu),
- `PERM_EDITING_SYSTEM_CONFIG` — uprawnienie do zarządzania konfiguracją witryny i systemu,
- `PERM_FILES_EDITOR_ACCESS` — uprawnienie do korzystania z edytora plików,
- `PERM_SUPER_USER` — uprawnienie do korzystania z edytora konfiguracji i innych elementów systemu.

## *static* `getByName($name)`

Zwraca użytkownika o nazwie `$name` lub fałsz, jeśli brak takiego użytkownika.