User
===

Klasa reprezentująca użytkownika panelu administracyjnego (rekord w tabeli bazy danych). Dziedziczy po klasie `DatabaseObject`.

Posiada następujące pola:

- `id` — klucz podstawowy;
- `name` — unikalna nazwa użytkownika;
- `password` — zahaszowane hasło użytkownika; nie należy modyfikować tego pola, zamiast tego należy używać metod `setPassword()` oraz `checkPassword()`;
- `permissions` — poziom uprawnień użytkownika zapisany w formie liczby całkowitej będącej sumą wartości stałych określających uprawnienia (patrz niżej);
- `createdTime` — data i czas utworzenia użytkownika w formie uniksowego znacznika czasu.

W klasie zdefiniowane zostały stałe służące do określania poziomu uprawnień użytkownika.

- `PERM_CREATE_PAGES` — uprawnienie do tworzenia szkiców stron oraz do edycji stron i szkiców należących do użytkownika;
- `PERM_PUBLISH_PAGES` — uprawnienie do publikowania tych szkiców stron i przenoszenia tych stron do szkiców, do których ma się uprawnienie;
- `PERM_EDIT_PAGES` — uprawnienie do modyfikacji wszystkich stron, również należących do innych użytkowników, i zmiany właścicieli stron;
- `PERM_MANAGE_PAGES` — suma uprawnień `PERM_CREATE_PAGES`, `PERM_PUBLISH_PAGES` i `PERM_EDIT_PAGES` (uwaga: przy sprawdzaniu bieżących uprawnień użytkownika należy używać tej stałej wyłącznie, gdy oczekiwane jest posiadanie któregokolwiek z tych uprawnień, nie — gdy oczekiwane są wyłącznie wszystkie jednocześnie);
- `PERM_MANAGE_FILES` — uprawnienie do wysyłania plików na serwer i zarządzania wszystkimi wysłanymi plikami;
- `PERM_WEBSITE_ELEMENTS` — uprawnienie do modyfikacji obszarów i menu witryny;
- `PERM_WEBSITE_SETTINGS` — uprawnienie do zarządzania konfiguracją witryny i systemu;
- `PERM_SUPER_USER` — uprawnienie do zarządzania kontami użytkowników oraz korzystania z edytora konfiguracji, edytora plików i innych elementów systemu.

## *static* `getByName(string $name) : ?User`

Zwraca użytkownika o nazwie `$name` lub fałsz, jeśli brak takiego użytkownika.

## `setPassword(string $givenPassword) : void`

Ustawia hasło użytkownika na podane w argumencie `$givenPassword` z wykorzystaniem wbudowanych w PHP funkcji do obsługi haseł. Po wywołaniu tej metody w polu `password` zostanie umieszczony hasz hasła.

## `checkPassword(string $givenPassword) : bool`

Porównuje hasło podane w argumencie `$givenPassword` z haszem hasła zapisanym w bazie danych w polu `password` z wykorzystaniem wbudowanych w PHP funkcji do obsługi haseł. Zwraca prawdę, jeśli podane hasło jest poprawne, inaczej fałsz.