UserSession
---

Menadżer sesji użytkownika. Zarządza sesją, umożliwia zalogowanie i wylogowanie użytkownika.

Rozpoczęcie sesji powoduje utworzenie po stronie klienta ciastka HTTP z identyfikatorem sesji. Dane sesji przypisane do jej identyfikatora (identyfikator użytkownika, data ważności sesji, WAI) są przechowywane w pliku konfiguracyjnym systemu — po stronie serwera. Przy wylogowaniu ciastko HTTP i dane z pliku konfiguracyjnego są usuwane.

WAI to hasz sha512 z ciągu znaków złożonego z informacji o przeglądarce i użytkowniku (m.in. user agent i adres IP). WAI to skrót od „where am I?”.

Menadżer przed użyciem powinien zostać zainicjowany przy użyciu metody `setup()`.

## `setup()`

Inicjuje menadżera sesji użytkownika. Jeśli sesja straciła ważność lub WAI jest niewłaściwy, sesja jest niszczona.

## `logIn($userId, $sessionDuration)`

Dokonuje zalogowania użytkownika o identyfikatorze określonym w argumencie `$userId`. Argument `$sessionDuration` określa czas trwania sesji użytkownika w sekundach.

Jeżeli użytkownik już jest zalogowany bądź menadżer nie został zainicjowany, zostanie rzucony wyjątek #17.

## `logOut()`

Wylogowuje aktualnie zalogowanego użytkownika.

Jeżeli żaden użytkownik nie jest zalogowany bądź menadżer nie został zainicjowany, zostanie rzucony wyjątek #18.

## `isLoggedIn()`

Zwraca prawdę, jeśli użytkownik jest zalogowany bądź fałsz, jeśli nie jest.

## `getUserId()`

Zwraca identyfikator zalogowanego użytkownika, bądź fałsz, jeśli użytkownik nie jest zalogowany.