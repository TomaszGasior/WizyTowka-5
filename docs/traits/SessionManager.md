SessionManager
===

Menadżer sesji użytkownika. Zarządza sesją, umożliwia zalogowanie i wylogowanie użytkownika.

Rozpoczęcie sesji powoduje utworzenie po stronie klienta ciastka HTTP z identyfikatorem sesji. Dane sesji przypisane do jej identyfikatora (identyfikator użytkownika, data ważności sesji, WAI) są przechowywane w pliku konfiguracyjnym po stronie serwera. Identyfikator sesji jest, dla zwiększenia bezpieczeństwa, automatycznie zmieniany co kilka minut. Przy wylogowaniu ciastko HTTP i dane z pliku konfiguracyjnego są usuwane.

WAI („where am I?”) — to ciąg znaków używany do zidentyfikowania środowiska (przeglądarki internetowej) używanego przy zalogowaniu. Jest to hasz sha512 z ciągu znaków złożonego z informacji o przeglądarce i użytkowniku (m.in. user agent i adres IP).

Sesje są przechowywane w pliku konfiguracyjnym `sessions.conf`, domyślnie w lokalizacji `data/config`.

Menadżer przed użyciem powinien zostać zainicjowany przy użyciu metody `setup()`.

## *static* `setup()`

Inicjuje menadżera sesji użytkownika. Jeśli sesja straciła ważność lub WAI jest niewłaściwy, sesja jest niszczona.

Jeżeli menadżer sesji został już uruchomiony, metoda rzuca wyjątek `SessionManagerException` #1.

## *static* `logIn($userId, $sessionDuration)`

Dokonuje zalogowania użytkownika o identyfikatorze określonym w argumencie `$userId`. Argument `$sessionDuration` określa czas trwania sesji użytkownika w sekundach.

Uwaga: użytkownik zostanie zalogowany dopiero przy następnym żądaniu HTTP! W bieżącym żądaniu menadżer sesji będzie zachowywać się, jakby użytkownik nie był zalogowany. Zalogowanie nie nastąpi, jeśli klient ma wyłączoną funkcję ciasteczek w przeglądarce.

Jeżeli użytkownik już jest zalogowany bądź menadżer nie został zainicjowany, zostanie rzucony wyjątek `SessionManagerException` #2.

## *static* `logOut()`

Wylogowuje aktualnie zalogowanego użytkownika oraz usuwa z pliku konfiguracyjnego wszystkie sesje, których ważność upłynęła.

Jeżeli żaden użytkownik nie jest zalogowany bądź menadżer nie został zainicjowany, zostanie rzucony wyjątek `SessionManagerException` #2.

## *static* `isUserLoggedIn()`

Zwraca prawdę, jeśli użytkownik jest zalogowany bądź fałsz, jeśli nie jest.

## *static* `getUserId()`

Zwraca identyfikator zalogowanego użytkownika, bądź fałsz, jeśli użytkownik nie jest zalogowany.