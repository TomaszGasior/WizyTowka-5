SessionManager
===

Menadżer sesji użytkownika. Zarządza sesją, umożliwia zalogowanie i wylogowanie użytkownika.

Rozpoczęcie sesji powoduje utworzenie po stronie klienta ciastka HTTP z identyfikatorem sesji. Dane sesji przypisane do jej identyfikatora (identyfikator użytkownika, data ważności sesji, WAI) są przechowywane w pliku konfiguracyjnym systemu — po stronie serwera. Przy wylogowaniu ciastko HTTP i dane z pliku konfiguracyjnego są usuwane.

WAI to hasz sha512 z ciągu znaków złożonego z informacji o przeglądarce i użytkowniku (m.in. user agent i adres IP). WAI to skrót od „where am I?”.

Menadżer przed użyciem powinien zostać zainicjowany przy użyciu metody `setup()`.

## *static* `setup()`

Inicjuje menadżera sesji użytkownika. Jeśli sesja straciła ważność lub WAI jest niewłaściwy, sesja jest niszczona.

Jeżeli menadżer sesji został już uruchomiony, metoda rzuca wyjątek `SessionManagerException` #1.

## *static* `logIn($userId, $sessionDuration)`

Dokonuje zalogowania użytkownika o identyfikatorze określonym w argumencie `$userId`. Argument `$sessionDuration` określa czas trwania sesji użytkownika w sekundach.

Jeżeli użytkownik już jest zalogowany bądź menadżer nie został zainicjowany, zostanie rzucony wyjątek `SessionManagerException` #2.

## *static* `logOut()`

Wylogowuje aktualnie zalogowanego użytkownika.

Jeżeli żaden użytkownik nie jest zalogowany bądź menadżer nie został zainicjowany, zostanie rzucony wyjątek `SessionManagerException` #2.

## *static* `isUserLoggedIn()`

Zwraca prawdę, jeśli użytkownik jest zalogowany bądź fałsz, jeśli nie jest.

## *static* `getUserId()`

Zwraca identyfikator zalogowanego użytkownika, bądź fałsz, jeśli użytkownik nie jest zalogowany.

## *static private* `_generateWAI($userId)`

Metoda generuje ciąg znaków WAI używany do zidentyfikowania środowiska (przeglądarki internetowej) używanego przy zalogowaniu.

WAI to hasz sha512 z ciągu znaków złożonego z informacji o przeglądarce i użytkowniku (m.in. user agent i adres IP). WAI to skrót od „where am I?”.

## *static private* `_getSessionsConfig()`

Używana wewnętrznie metoda pobierająca zawartość pliku konfiguracyjnego `sessions.conf`. Plik ten przechowuje informacje o sesjach użytkowników.