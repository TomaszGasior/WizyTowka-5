User
===

Klasa reprezentująca użytkownika panelu administracyjnego (rekord w tabeli bazy danych). Dziedziczy po klasie `DatabaseObject`.

Posiada następujące pola:

- `id` — klucz podstawowy,
- `name` — unikalna nazwa użytkownika,
- `password` — zahaszowane hasło użytkownika; nie należy modyfikować tego pola, zamiast tego należy używać metod `setPassword()` oraz `checkPassword()`,
- `permissions` — poziom uprawnień użytkownika zapisany w formie liczby całkowitej będącej sumą wartości stałych określających uprawnienia (patrz niżej),
- `createdTime` — data i czas utworzenia użytkownika w formie uniksowego znacznika czasu.

W klasie zdefiniowane zostały stałe służące do określania poziomu uprawnień użytkownika.

- `PERM_CREATING_PAGES` — uprawnienie do tworzenia stron i szkiców stron w witrynie,
- `PERM_SENDING_FILES` — uprawnienie do wysyłania plików na serwer,
- `PERM_EDITING_OTHERS_PAGES` — uprawnienie do edycji stron stworzonych przez innych użytkowników,
- `PERM_EDITING_SITE_ELEMENTS` — uprawnienie do modyfikacji elementów witryny (nagłówek, stopka, menu),
- `PERM_EDITING_SITE_CONFIG` — uprawnienie do zarządzania konfiguracją witryny i systemu,
- `PERM_SUPER_USER` — uprawnienie do zarządzania kontami użytkowników oraz korzystania z edytora konfiguracji, edytora plików i innych elementów systemu.
- `PERM_ALL` — wszystkie powyższe uprawniania łącznie.

## *static* `getByName($name)`

Zwraca użytkownika o nazwie `$name` lub fałsz, jeśli brak takiego użytkownika.

## `setPassword($givenPassword)`

Ustawia hasło użytkownika na podane w argumencie `$givenPassword` z wykorzystaniem wbudowanych w PHP funkcji do obsługi haseł. Po wywołaniu tej metody w polu `password` zostanie umieszczony hasz hasła.

## `checkPassword($givenPassword)`

Porównuje hasło podane w argumencie `$givenPassword` z haszem hasła zapisanym w bazie danych w polu `password` z wykorzystaniem wbudowanych w PHP funkcji do obsługi haseł. Zwraca prawdę, jeśli podane hasło jest poprawne, inaczej fałsz.