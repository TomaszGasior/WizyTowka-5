User
===

Klasa reprezentująca użytkownika panelu administracyjnego (rekord w tabeli bazy danych). Dziedziczy po klasie `DatabaseObject`.

Posiada następujące pola:

- `id` — klucz podstawowy,
- `name` — unikalna nazwa użytkownika,
- `password` — zahaszowane hasło użytkownika,
- `createdTime` — data i czas utworzenia użytkownika w formie uniksowego znacznika czasu.

## *static* `getByName($name)`

Zwraca użytkownika o nazwie `$name` lub fałsz, jeśli brak takiego użytkownika.